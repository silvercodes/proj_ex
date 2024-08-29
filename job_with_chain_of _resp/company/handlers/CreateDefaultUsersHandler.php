<?php


namespace app\modules\admin\modules\v1\components\job\company\handlers;


use app\modules\admin\modules\v1\components\job\company\exceptions\CompanyDeploymentException;
use app\modules\admin\modules\v1\models\Company;
use app\modules\v1\modules\company\models\User;
use Exception;
use itstep\base\DynamicServiceLocator;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;

/**
 * Class CreateDefaultUsersHandler
 * @package app\modules\admin\modules\v1\components\job\company\handlers
 */
class CreateDefaultUsersHandler extends AbstractJobHandler
{
    /**
     * @var Company
     */
    protected $company;

    /**
     * @var array|mixed
     */
    private $adminCredentials = [];

    /**
     * CreateDefaultUsersHandler constructor.
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        parent::__construct();

        $this->company = $company;

        if (! empty($this->company->admin_credential)) {
            $this->adminCredentials = json_decode($this->company->admin_credential, true);
        }
    }

    /**
     * Executing handler task
     *
     * @return array
     * @throws CompanyDeploymentException
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function execute(): array
    {
        try {
            /** @var Connection $connection */
            $connection = Yii::$app->getDynamicLocator()->doOnBehalf($this->company->alias, function (DynamicServiceLocator $locator) {
                return $locator->get('db');
            });

            if (! empty($this->adminCredentials)) {
                $this->createUser($connection, User::USER_ID_ADMIN, [
                        'email'         => $this->adminCredentials['adminEmail'],
                        'first_name'    => $this->adminCredentials['adminFirstName'],
                        'last_name'     => $this->adminCredentials['adminLastName'],
                        'hash'          => Yii::$app->security->generatePasswordHash($this->adminCredentials['adminPass']),
                        'status'        => User::STATUS_ACTIVE,
                        'access'        => User::ACCESS_ADMIN
                    ]);
            } else {
                throw new CompanyDeploymentException(
                    'Admin credentials is empty',
                    CompanyDeploymentException::DEPLOY_CREDENTIALS_EMPTY
                );
            }

            $this->putLogs([
                'success' => true,
                'message' => 'Default users was created successfully.',
            ]);

            return parent::execute();
        } catch (Exception $e) {
            $this->rollback();

            throw $e;
        }
    }

    public function rollback()
    {
        // rollback is not needed, the database will be deleted in the previous step
    }


    /**
     * @param Connection $connection
     * @param $id
     * @param array $data
     * @throws \yii\db\Exception
     */
    private function createUser(Connection $connection, $id, array $data)
    {
        $table = $connection->quoteTableName('{{%user}}');

        $connection->createCommand()->insert($table, $data)->execute();

        // TODO:NOTE SIN-301
        // Explicitly update the admin id to 1
        // Due to the fact that the database reserves values for primary_key, sometimes it was impossible to insert a user with id = 1 explicitly
        $insertedId = $connection->getLastInsertID();
        $connection->createCommand()->update($table, [
            'id' => $id
        ], "id = $insertedId")
            ->execute();

        $connection->createCommand()->insert($connection->quoteTableName('{{%user_profile}}'), [
            'user_id'   => $id,
            'phone'     => $id === User::USER_ID_ADMIN ? $this->adminCredentials['adminPhone'] : '',
        ])->execute();

        $connection->createCommand()->insert($connection->quoteTableName('{{%auth_assignment}}'), [
            'item_name'     => 'admin',
            'user_id'       => $id,
            'created_at'    => time()
        ])->execute();
    }
}
