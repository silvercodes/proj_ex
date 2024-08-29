<?php


namespace app\modules\admin\modules\v1\components\job\company\handlers;


use app\modules\admin\modules\v1\components\job\company\exceptions\CompanyDeploymentException;
use app\modules\admin\modules\v1\models\Company;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIKey;
use Cloudflare\API\Endpoints\DNS;
use Exception;
use Yii;

/**
 * Class CreateDomainHandler
 * @package app\modules\admin\modules\v1\components\job\company\handlers
 */
class CreateDomainHandler extends AbstractJobHandler
{
    /**
     * @var Company
     */
    protected $company;

    /**
     * CreateDomainHandler constructor.
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
     * @throws CompanyDeploymentException
     */
    public function execute(): array
    {
        try {
            if (YII_ENV_PROD || YII_ENV_TEST) {
                $this->createDomain($this->company->alias, $this->company->domain_name);

                $this->putLogs([
                    'success' => true,
                    'message' => "Domain {$this->company->alias}{$this->company->domain_name} was created successfully.",
                ]);
            }

            return parent::execute();
        } catch (Exception $e) {
            // $this->rollback(); // not needed because the domain was not created

            throw $e;
        }
    }

    /**
     * Rollback handler task
     */
    public function rollback()
    {
        if (YII_ENV_PROD || YII_ENV_TEST) {
            $this->deleteDomain($this->company->alias, $this->company->domain_name);
        }
    }

    /**
     * Create domain
     *
     * @param string $alias
     * @param string $domain_name
     * @throws CompanyDeploymentException
     */
    private function createDomain(string $alias, string $domain_name)
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
        if (! $result) {
            throw new CompanyDeploymentException(
                "Domain $alias.$domain create failed.",
                CompanyDeploymentException::DEPLOY_CREATE_DOMAIN_ERROR
            );
        }
    }

    /**
     * Delete domain
     *
     * @param string $alias
     * @param string $domain_name
     * @return bool
     */
    private function deleteDomain(string $alias, string $domain_name): bool
    {
        $params = Yii::$app->params;
//        $key = new APIKey($params['AI_CLOUDFLARE_EMAIL'], $params['AI_CLOUDFLARE_APIKEY']);
        $key = new CloudflareApiKey($params['AI_CLOUDFLARE_APIKEY']);
        $adapter = new Guzzle($key);

        $dns = new DNS($adapter);
        $domain = Company::DOMAIN_NAME["$domain_name"]['domain'];
        $zoneId = Company::DOMAIN_NAME["$domain_name"]['zone_id'];

        $id = $dns->getRecordID($zoneId, '', $alias.$domain);

        return $dns->deleteRecord($zoneId, $id);
    }
}
