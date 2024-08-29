<?php


namespace app\modules\admin\modules\v1\components\job\company\handlers;


use app\modules\admin\modules\v1\models\Company;
use Exception;
use Yii;

/**
 * Class MigrateHandler
 * @package app\modules\admin\modules\v1\components\job\company\handlers
 */
class MigrateHandler extends AbstractJobHandler
{
    /**
     * @var Company
     */
    protected $company;

    /**
     * MigrateHandler constructor.
     * @param Company $company
     */
    public function __construct(Company $company)
    {
        parent::__construct();

        $this->company = $company;
    }

    /**
     * Executing handler task
     *
     * @return array
     * @throws Exception
     */
    public function execute(): array
    {
        try {
            $root = Yii::getAlias('@app');

            $commands = [
                'main_migrations' => "php $root/yii v1/company/migrate/up -A=\"{$this->company->alias}\" --interactive=0 2>&1",
                'gaming_migrations' => "php $root/yii v1/company/migrate/up --migrationNamespaces=\"v1\company\gaming\migrations\" -A=\"{$this->company->alias}\" --interactive=0 2>&1",
                'settings_migrations' => "php $root/yii v1/company/migrate/up --migrationNamespaces=\"core\settings\migrations\" -A=\"{$this->company->alias}\" --interactive=0 2>&1",
            ];

            $execLog = $this->runMigrations($commands);

            $this->putLogs([
                'success' => true,
                'message' => 'Migrations are successful',
                'exec_output' => $execLog
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
     * Execute migrations commands
     *
     * @param array $commands
     * @return array
     */
    private function runMigrations(array $commands): array
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

}
