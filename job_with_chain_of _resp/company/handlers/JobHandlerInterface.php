<?php


namespace app\modules\admin\modules\v1\components\job\company\handlers;


/**
 * Interface JobHandlerInterface
 * @package app\modules\admin\modules\v1\components\job\company\handlers
 */
interface JobHandlerInterface
{
    /**
     * Set next job handler in execution chain
     *
     * @param JobHandlerInterface $jobPoint
     * @return JobHandlerInterface
     */
    public function setNext(JobHandlerInterface $jobPoint): JobHandlerInterface;

    /**
     * Executing handler task
     *
     * @return array
     */
    public function execute(): array;

    /**
     * Rollback handler task
     *
     * @return mixed
     */
    public function rollback();

}
