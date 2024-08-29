<?php


namespace app\modules\admin\modules\v1\components\job\company\handlers;


use app\modules\admin\modules\v1\models\Company;
use core\helpers\EmailHelper;
use Exception;
use Yii;

/**
 * Class NotifyUsersHandler
 * @package app\modules\admin\modules\v1\components\job\company\handlers
 */
class NotifyUsersHandler extends AbstractJobHandler
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
     * CreateDomainHandler constructor.
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
     * @throws Exception
     */
    public function execute(): array
    {
        try {
            $this->sendAdminEmail();
            $this->sendPartnerEmail();

            $this->putLogs([
                'success' => true,
                'message' => "Notifications sent.",
            ]);

            return parent::execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function rollback()
    {
        // rollback is not needed
    }

    /**
     * Send email to company admin user
     */
    protected function sendAdminEmail(): void
    {
        Yii::$app->mailer
            ->compose('@app/modules/admin/modules/v1/views/email/admin_email.php', [
                'lang'      => $this->company->language,
                'login'     => $this->adminCredentials['adminEmail'],
                'password'  => $this->adminCredentials['adminPass'],
                'alias'     => $this->company->alias,
                'domain'    => Company::DOMAIN_NAME["{$this->company->domain_name}"]['domain']
            ])
            ->setFrom(EmailHelper::getNoReplyEmail())
            ->setTo($this->adminCredentials['adminEmail'])
            ->setSubject(Yii::t('app', 'Registration', [], $this->company->language))
            ->send();
    }

    /**
     * Send email to partner
     */
    protected function sendPartnerEmail(): void
    {
        Yii::$app->mailer
            ->compose('@app/modules/admin/modules/v1/views/email/partner_email.php', [
                'alias'         => $this->company->alias,
                'adminName'     => "{$this->adminCredentials['adminLastName']} {$this->adminCredentials['adminFirstName']}",
                'adminEmail'    => "{$this->adminCredentials['adminEmail']}",
                'adminPhone'    => "{$this->adminCredentials['adminPhone']}",
            ])
            ->setFrom(EmailHelper::getNoReplyEmail())
            ->setTo($this->company->partner->email)
            ->setSubject('NEW company registered!')
            ->send();
    }
}
