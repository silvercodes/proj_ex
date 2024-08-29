<?php


namespace app\modules\admin\modules\v1\components\job\company;


use app\modules\admin\modules\v1\components\job\BaseAdminCompanyJob;
use app\modules\admin\modules\v1\components\job\company\handlers\CreateDbHandler;
use app\modules\admin\modules\v1\components\job\company\handlers\CreateDefaultUsersHandler;
use app\modules\admin\modules\v1\components\job\company\handlers\CreateDomainHandler;
use app\modules\admin\modules\v1\components\job\company\handlers\ImportTracksHandler;
use app\modules\admin\modules\v1\components\job\company\handlers\MigrateHandler;
use app\modules\admin\modules\v1\components\job\company\handlers\NotifyUsersHandler;
use app\modules\admin\modules\v1\models\Company;
use DateTime;
use Exception;
use Yii;
use yii\db\StaleObjectException;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class CompanyDeploymentJob
 * @package app\modules\admin\modules\v1\components\job\company
 */
class CompanyDeploymentJob extends BaseAdminCompanyJob implements JobInterface
{
    /**
     * @param Queue $queue
     * @return void
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function execute($queue)
    {
        $companyId = $this->data['id'];

        /* @var $company Company */
        $company = Company::findOne(['id' => $companyId]);

        $createDbHandler = new CreateDbHandler($company);

        $createDbHandler
            ->setNext(new MigrateHandler($company))
            ->setNext(new CreateDefaultUsersHandler($company))
            ->setNext(new ImportTracksHandler($company))
            ->setNext(new CreateDomainHandler($company))
            ->setNext(new NotifyUsersHandler($company));

        $logData = [
            'company_alias' => $company->alias,
        ];

        try {
            $logs = $createDbHandler->execute();
            $logData['logs'] = $logs;

            $company->status = Company::STATUS_ON;

            $todayTimestamp = (new DateTime('today'))->getTimestamp();
            $boundaryDateTimestamp = DateTime::createFromFormat('Y-m-d', Company::GRACE_MODE_BOUNDARY_DATE)
                ->setTime(0, 0)->getTimestamp();

            if ($todayTimestamp >= $boundaryDateTimestamp) {
                $company->grace_mode_status = Company::GRACE_MODE_STATUS_AVAILABLE;
            }

            $company->save(false);
        } catch (Exception $e) {
            $logData['error'] = $e;

            $company->delete();
        } finally {
            Yii::info($logData, 'company_deployment');
        }
    }

}
