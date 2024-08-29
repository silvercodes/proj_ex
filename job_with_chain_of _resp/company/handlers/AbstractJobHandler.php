<?php


namespace app\modules\admin\modules\v1\components\job\company\handlers;


/**
 * Class AbstractJobHandler
 * @package app\modules\admin\modules\v1\components\job\company\handlers
 */
abstract class AbstractJobHandler implements JobHandlerInterface
{
    /**
     * @var JobHandlerInterface
     */
    private $next;

    /**
     * logs array
     * @var array
     */
    private $logsBag;

    /**
     * AbstractJobHandler constructor.
     */
    protected function __construct()
    {
        $this->logsBag = [];
    }

    /**
     * Put logs to logs array
     *
     * @param array $logs
     */
    protected function putLogs(array $logs)
    {
        $this->logsBag[get_called_class()] = $logs;
    }

    /**
     * Set next job handler in execution chain
     *
     * @param JobHandlerInterface $jobPoint
     * @return JobHandlerInterface
     */
    public function setNext(JobHandlerInterface $jobPoint): JobHandlerInterface
    {
        $this->next = $jobPoint;

        return $jobPoint;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        if ($this->next) {
            return array_merge($this->logsBag, $this->next->execute());
        }

        return $this->logsBag;
    }

    /**
     * @return mixed
     */
    public abstract function rollback();

}
