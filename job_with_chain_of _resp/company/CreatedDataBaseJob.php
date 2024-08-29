<?php

declare(strict_types=1);

namespace app\modules\admin\modules\v1\components\job\company;

use app\modules\admin\modules\v1\components\job\BaseAdminCompanyJob;
use app\modules\admin\modules\v1\components\job\company\handlers\CloudflareApiKey;
use app\modules\admin\modules\v1\models\Company;
use app\modules\admin\modules\v1\models\PositionImport;
use app\modules\v1\modules\company\models\PositionGroup;
use app\modules\v1\modules\company\models\User;
use app\modules\v1\modules\company\services\MoveTrackService;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use core\helpers\EmailHelper;
use itstep\base\DynamicServiceLocator;
use Yii;
use yii\base\Exception;
use yii\db\Connection;
use yii\db\mssql\Schema;
use yii\queue\RetryableJobInterface;
use yii\web\ServerErrorHttpException;

class CreatedDataBaseJob extends BaseAdminCompanyJob implements RetryableJobInterface
{
    public function getTtr()
    {
        return 3600;
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 2);
    }

    /**
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function execute($queue)
    {
        $model = $this->data;

        $logData = [
            'company_alias' => $model->alias,
        ];

            //проверка на существование БД
        $company = Company::findOne(['alias' => $model->alias]);
        if($company->checkDBConnection()) {
            $logData['ERROR'] = 'Database exists';
            Yii::info($logData, 'company_deployment');

            exit();
        }

        $connectionParams = $this->createConnectionParams($model);

        try {
            $connection = new Connection($connectionParams);
            $connection->open();
        } catch (Exception $e) {
            $model->addError(
                'db_name',
                Yii::t(
                    'yii',
                    'Couldn\'t connect to DB with these params!'
                ).'<br />'.$connectionParams['dsn'].'<br />'.$e->getMessage()
            );

            $logData['ERROR'] = [
                'error' => 'DB connection params is invalid',
                'connection_params' => $connectionParams,
                'message' => $e->getMessage(),
            ];

            Yii::info($logData, 'company_deployment');

            $connection->close();

            exit();
        }

        try {
            $sql = 'CREATE DATABASE IF NOT EXISTS '
                . ($connection->schema instanceof Schema ? ' `'.$model->connection->db_name_prefix . $model->alias.'` CHARACTER SET utf8 COLLATE utf8_general_ci;'
                    : '`' . $model->connection->db_name_prefix . $model->alias . '`');
            $connection->createCommand($sql)->execute();

            $connectionParams = $this->createConnectionParams($model);
            $connectionParams['dsn'] = $connectionParams['dsn'] . 'dbname=' . $model->connection->db_name_prefix . $model->alias . '';
            $connection = new Connection($connectionParams);
            $connection->open();
        } catch (Exception $e) {

            $logData['ERROR'] = [
                'error' => 'DB creating or new connection failed',
                'connection_params' => $connectionParams,
                'message' => $e->getMessage(),
            ];

            Yii::info($logData, 'company_deployment');

            $connection->close();

            exit();
        }

        $dbName = 'dbname=' . $model->connection->db_name_prefix . $model->alias . '';
        $connectionParams = $this->createConnectionParams($model);
        $connectionParams['dsn'] = $connectionParams['dsn'] . $dbName;
        $connection = new Connection($connectionParams);
        $connection->open();

        // TODO: изменить транзакцию
        $transaction = Yii::$app->getDb()->beginTransaction();

        try {
            if (YII_ENV_PROD || YII_ENV_TEST) {
                $this->createDomain($model->alias, $model->domain_name);
            }

            $root = Yii::getAlias('@app');

            $commands = [
                'main_migrations' => "php {$root}/yii v1/company/migrate/up -A=\"{$model->alias}\" --interactive=0 2>&1",
                'gaming_migrations' => "php {$root}/yii v1/company/migrate/up --migrationNamespaces=\"v1\company\gaming\migrations\" -A=\"{$model->alias}\" --interactive=0 2>&1",
                'settings_migrations' => "php {$root}/yii v1/company/migrate/up --migrationNamespaces=\"core\settings\migrations\" -A=\"{$model->alias}\" --interactive=0 2>&1",
            ];

            $execLog = $this->executeCommands($commands);

            $logData = array_merge($logData, $execLog);
            Yii::info($logData, 'company_deployment');

            if(!empty($model->admin_credential)){
                $adminCredential = json_decode( $model->admin_credential, true);
                $this->createAdminUser($this->data->alias, $adminCredential);
                $this->sendEmail($adminCredential['adminEmail'], $adminCredential['adminPass'], $this->data->alias, $this->data->domain_name, $this->data->language);
                $this->sendPartnerEmail($model, $adminCredential);
            }
            $this->createSupportUser($this->data->alias, Company::SUPPORT_USER, '$2y$10$GW5dtPjAF0veOxFRJ/KD/OCbfy9Sxw6MpQjM2x6AujHUeF3NtwApS');
            $this->startImport($this->data->alias);
            $this->updatePositionGroupLanguage($this->data->alias, $this->data->language);

            $company = Company::findOne(['id' => $model->id]);
            $company->status = Company::STATUS_ON;
            $company->save();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();

            $logData['ERROR'] = [
                'message' => $e,
            ];

            Yii::info($logData, 'company_deployment');

        } finally {
            $connection->close();
        }
    }

    /**
     * Запуск команд
     *
     * @param array $commands
     * @return array
     */
    protected function executeCommands(array $commands): array
    {
        $output = null;
        $status = null;
        $logData = [];

        foreach($commands as $tag => $cmd) {
            exec($cmd, $output, $status);

            $logData[$tag] = [
                'status' => $status,
                'output' => $output,
            ];
            unset($output);
        }

        return $logData;
    }

    /**
     * Ф-ция добавляет администратора в компанию.
     *
     * @param string $alias
     * @param array $adminCredential
     */
    protected function createAdminUser(string $alias, array $adminCredential): void
    {
        Yii::$app->getDynamicLocator()->doOnBehalf($alias, function (DynamicServiceLocator $locator) use ($adminCredential) {
            /** @var Connection $connection */
            $connection = $locator->get('db');
            $userTable = $connection->quoteTableName('{{%user}}');

            $connection->createCommand()->insert($userTable, [
                'email'         => $adminCredential['adminEmail'],
                'first_name'    => $adminCredential['adminFirstName'],
                'last_name'     => $adminCredential['adminLastName'],
                'hash'          => Yii::$app->security->generatePasswordHash($adminCredential['adminPass']),
                'status'        => User::STATUS_ACTIVE,
                'access'        => User::ACCESS_ADMIN
            ])->execute();

            // TODO:NOTE SIN-301
            // Явное обновление id админа на 1
            // Из-за того, что БД резервирует значения для primary_key иногда невозможно было вставить юзера с id = 1
            $insertedId = $connection->getLastInsertID();
            $connection->createCommand()->update($userTable, [
                    'id' => User::USER_ID_ADMIN
                ], "id = $insertedId")
            ->execute();

            $connection->createCommand()->insert($connection->quoteTableName('{{%user_profile}}'), [
                'user_id'   => User::USER_ID_ADMIN,
                'phone'     => $adminCredential['adminPhone'],
            ])->execute();

            $connection->createCommand()->insert($connection->quoteTableName('{{%auth_assignment}}'), [
                'item_name'     => 'admin',
                'user_id'       => User::USER_ID_ADMIN,
                'created_at'    => time()
            ])->execute();
        });
    }

    /**
     * Ф-ция для отправки доступов администратору компании.
     *
     * @param string $email
     * @param string $password
     * @param string $alias
     * @param string $domain_name
     * @param string $lang // язык компании
     */
    protected function sendEmail(string $email, string $password, string $alias, string $domain_name, string $lang): void
    {
        Yii::$app->mailer
            ->compose('@app/modules/admin/modules/v1/views/email/admin_email.php', [
                'lang'      => $lang,
                'login'     => $email,
                'password'  => $password,
                'alias'     => $alias,
                'domain'    => Company::DOMAIN_NAME["$domain_name"]['domain']
            ])
            ->setFrom(EmailHelper::getNoReplyEmail())
            ->setTo($email)
            ->setCc('milkevich@itstep.org')
            ->setSubject(Yii::t('app', 'Registration', [], $lang))
            ->send();
    }

    /**
     * Ф-ция для отправки инфо письма партнеру о создании компании.
     *
     * @param Company $company
     * @param array $adminCredential
     */
    protected function sendPartnerEmail(Company $company, array $adminCredential): void
    {
        Yii::$app->mailer
            ->compose('@app/modules/admin/modules/v1/views/email/partner_email.php', [
                'alias'         => $company->alias,
                'adminName'     => "{$adminCredential['adminLastName']} {$adminCredential['adminFirstName']}",
                'adminEmail'    => "{$adminCredential['adminEmail']}",
                'adminPhone'    => "{$adminCredential['adminPhone']}",
            ])
            ->setFrom(EmailHelper::getNoReplyEmail())
            ->setTo($company->partner->email)
            ->setSubject('NEW company registered!')
            ->send();
    }

    /**
     * Ф-ция добавляет пользователя support в компанию.
     *
     * @param string $alias
     * @param string $login
     * @param string $password
     */
    protected function createSupportUser(string $alias, string $login, string $password): void
    {
        Yii::$app->getDynamicLocator()->doOnBehalf($alias, function (DynamicServiceLocator $locator) use ($login, $password) {
            /** @var Connection $connection */
            $connection = $locator->get('db');
            $userTable = $connection->quoteTableName('{{%user}}');
            $connection->createCommand()->insert($userTable, [
                'email'         => $login,
                'first_name'    => 'User',
                'last_name'     => 'Support',
                'hash'          => $password,
                'status'        => User::STATUS_ACTIVE,
                'access'        => User::ACCESS_ADMIN
            ])->execute();

            // TODO:NOTE SIN-301
            // Явное обновление id саппорта на 2
            // Из-за того, что БД резервирует значения для primary_key иногда невозможно было вставить юзера с id = 2
            $insertedId = $connection->getLastInsertID();
            $connection->createCommand()->update($userTable, [
                'id' => User::USER_ID_SUPPORT
            ], "id = $insertedId")
                ->execute();

            $connection->createCommand()->insert($connection->quoteTableName('{{%user_profile}}'), [
                'user_id' => User::USER_ID_SUPPORT,
            ])->execute();

            $connection->createCommand()->insert($connection->quoteTableName('{{%auth_assignment}}'), [
                'item_name' => 'admin',
                'user_id' => User::USER_ID_SUPPORT,
                'created_at' => time()
            ])->execute();
        });
    }

    /**
     * запуск импорта стартовых треков
     *
     * @param $alias
     */
    protected function startImport($alias){
        $positionsForImport = $this->getDataForImport();

        foreach ($positionsForImport as $key => $item) {
            $moveTrack = new MoveTrackService(
                1,
                $item->alias,
                $alias,
                $item->position_id
            );

            $moveTrack->runImport();
            unset($moveTrack);
        }
    }

    /**
     * Обновление раздела должностей по умолчанию на основании языка компании
     *
     * @param $alias
     * @param $language
     */
    protected function updatePositionGroupLanguage($alias, $language) {
        Yii::$app->getDynamicLocator()->doOnBehalf($alias, function (DynamicServiceLocator $locator) use ($language) {
            $connection = $locator->get('db');
            $groupName = Yii::t('app', 'Without section', [], $language);
            $connection->createCommand()->update($connection->quoteTableName(PositionGroup::tableName()),
                ['name' => $groupName],
                ['id' => PositionGroup::POSITION_GROUP_DEFAULT]
            )->execute();
        });
    }

    /**
     * Ф-ция возвращает параметры соеденения для компании.
     *
     * @param Company $company
     * @return array
     */
    protected function createConnectionParams(Company $company): array
    {
        $dbName = '';
        $dbType = 'mysql';

        $port = ($company->connection->port == '') ? '' : 'port='.$company->connection->port.';';
        $dsn = "{$dbType}:host={$company->connection->host};{$port}{$dbName}";

        $connectionParams = [
            'dsn'           => $dsn,
            'username'      => $company->connection->db_username,
            'password'      => $company->connection->db_password,
            'tablePrefix'   => $company->connection->table_prefix,
            'charset'       => 'utf8'
        ];

        return $connectionParams;
    }

    /**
     * Ф-ция добавлет поддомен компании на Cloudflare.
     *
     * @param string $alias
     * @param string $domain_name
     * @throws ServerErrorHttpException
     */
    protected function createDomain(string $alias, string $domain_name): void
    {
        $params = Yii::$app->params;
//        $key = new APIKey($params['AI_CLOUDFLARE_EMAIL'], $params['AI_CLOUDFLARE_APIKEY']);
        $key = new CloudflareApiKey($params['AI_CLOUDFLARE_APIKEY']);
        $adapter = new Guzzle($key);

        $dns = new DNS($adapter);
        $domain = Company::DOMAIN_NAME["$domain_name"]['domain'];
        $zoneId = Company::DOMAIN_NAME["$domain_name"]['zone_id'];

        $result = $dns->addRecord(
            $zoneId,
            'CNAME',
            $alias.$domain,
            $params['AI_CLOUDFLARE_CNAME'].$domain,
            1,
            true
        );
        if (!$result) {
            throw new ServerErrorHttpException('Failed to create subdomain.');
        }
    }

    /**
     * Функция возвращает данные для импортирования треков
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getDataForImport(){
        return PositionImport::find()
            ->alias('pi')
            ->innerJoin(['c' => Company::tableName()], 'pi.alias = c.alias')
            ->where(['c.status' => Company::STATUS_ON])
            ->all();
    }
}
