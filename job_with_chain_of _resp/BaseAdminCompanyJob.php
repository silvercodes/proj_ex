<?php

declare(strict_types=1);

namespace app\modules\admin\modules\v1\components\job;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\queue\JobInterface;

abstract class BaseAdminCompanyJob extends BaseObject implements JobInterface
{
    public $data;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (empty($this->data)) {
            throw new InvalidConfigException(__CLASS__ . '::$data can not be empty');
        }
    }
}
