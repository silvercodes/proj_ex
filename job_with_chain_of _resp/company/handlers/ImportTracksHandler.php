<?php


namespace app\modules\admin\modules\v1\components\job\company\handlers;


use app\modules\admin\modules\v1\models\Company;
use app\modules\admin\modules\v1\models\PositionImport;
use app\modules\v1\modules\company\models\PositionGroup;
use app\modules\v1\modules\company\services\MoveTrackService;
use Exception;
use itstep\base\DynamicServiceLocator;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class ImportTracksHandler
 * @package app\modules\admin\modules\v1\components\job\company\handlers
 */
class ImportTracksHandler extends AbstractJobHandler
{
    /**
     * @var Company
     */
    protected $company;

    /**
     * ImportTracksHandler constructor.
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
            $positionsForImport = $this->getDataForImport();
            foreach ($positionsForImport as $key => $item) {
                $moveTrackService = new MoveTrackService(
                    1,
                    $item->alias,
                    $this->company->alias,
                    $item->position_id
                );

                $moveTrackService->runImport();
                unset($moveTrackService);
            }

            $this->updatePositionGroupLanguage($this->company->alias, $this->company->language);

            $this->putLogs([
                'success' => true,
                'message' => 'Tracks import was successful.',
            ]);

            return parent::execute();
        } catch (Exception $e) {
            $this->rollback();

            throw $e;
        }
    }

    public function rollback()
    {
        // rollback is not needed
    }

    /**
     * Get data for importing tracks
     *
     * @return array|ActiveRecord[]
     */
    private function getDataForImport(): array
    {
        return PositionImport::find()
            ->alias('pi')
            ->innerJoin(['c' => Company::tableName()], 'pi.alias = c.alias')
            ->where(['c.status' => Company::STATUS_ON])
            ->all();
    }

    /**
     * Updating the default position section based on company language
     *
     * @param $alias
     * @param $language
     */
    protected function updatePositionGroupLanguage($alias, $language) {
        Yii::$app->getDynamicLocator()->doOnBehalf($alias, function (DynamicServiceLocator $locator) use ($language) {
            $connection = $locator->get('db');
            $groupName = Yii::t('app', 'Without section', [], $language);
            $connection->createCommand()->update($connection->quoteTableName(PositionGroup::tableName()),
                ['name' => $groupName],
                ['id' => PositionGroup::POSITION_GROUP_DEFAULT]
            )->execute();
        });
    }
}
