<?php


namespace app\modules\admin\modules\v1\components\job\company\exceptions;

use Exception;

/**
 * Class CompanyDeploymentException
 * @package app\modules\admin\modules\v1\components\job\company\exceptions
 */
class CompanyDeploymentException extends Exception
{
    const DEPLOY_CREATE_DB_ERROR = 10211;
    const DEPLOY_DATABASE_EXISTS = 10212;
    const DEPLOY_CREDENTIALS_EMPTY = 10214;
    const DEPLOY_CREATE_DOMAIN_ERROR = 10215;

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName(): string
    {
        return 'Company Deployment Exception';
    }
}
