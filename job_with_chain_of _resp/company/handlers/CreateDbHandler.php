<?php


namespace app\modules\admin\modules\v1\components\job\company\handlers;


use Yii;
use yii\db\Connection;
use yii\db\mssql\Schema;
use app\modules\admin\modules\v1\components\job\company\exceptions\CompanyDeploymentException;
use app\modules\admin\modules\v1\models\Company;
use Exception;

/**
 * Class CreateDbJobHandler
 * @package app\modules\admin\modules\v1\components\job\company\handlers
 */
class CreateDbHandler extends AbstractJobHandler
{
    /**
     * @var Company
     */
    protected $company;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * CreateDbJobHandler constructor.
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        parent::__construct();

        $this->company = $company;

        $this->connection = Yii::$app->db;
    }

    /**
     * Executing handler task
     *
     * @throws CompanyDeploymentException|\yii\db\Exception
     */
    public function execute(): array
    {
        try {
            if ($this->checkDbExists()) {
                throw new CompanyDeploymentException(
                    "DB {$this->company->databaseName} already exists.",
                    CompanyDeploymentException::DEPLOY_DATABASE_EXISTS
                );
            }

            if (! $this->createDatabase()) {
                throw new CompanyDeploymentException(
                    "DB {$this->company->databaseName} creation failed.",
                    CompanyDeploymentException::DEPLOY_CREATE_DB_ERROR
                );
            }

            $this->putLogs([
                'success' => true,
                'message' => "DB {$this->company->databaseName} created successfully."
            ]);

            return parent::execute();
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Rollback handler task
     */
    public function rollback()
    {
        try {
            if ($this->checkDbExists()) {
                $this->dropDatabase();
            }
        } catch (Exception $e) {
            // TODO: handling (logging to DeployAction)
        }
    }

    /**
     * Check that a database with the same name already exists
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    private function checkDbExists(): bool
    {
        $sql = "SELECT SCHEMA_NAME
                FROM INFORMATION_SCHEMA.SCHEMATA";

        $dbList = $this->connection->createCommand($sql)->queryColumn();

        return in_array($this->company->databaseName, $dbList);
    }

    /**
     * Create the database
     *
     * @return int
     * @throws \yii\db\Exception
     */
    private function createDatabase(): int
    {
        $sql = 'CREATE DATABASE IF NOT EXISTS '
            . ($this->connection->schema instanceof Schema ?
                ' `' .$this->company->connection->db_name_prefix . $this->company->alias.'` CHARACTER SET utf8 COLLATE utf8_general_ci;'
                : '`' . $this->company->connection->db_name_prefix . $this->company->alias . '`');

        return $this->connection->createCommand($sql)->execute();
    }

    /**
     * Drop database by name
     *
     * @throws \yii\db\Exception
     */
    private function dropDatabase(): void
    {
        $sql = "DROP DATABASE `{$this->company->databaseName}`;";

        $this->connection
            ->createCommand($sql)
            ->execute();
    }

}
