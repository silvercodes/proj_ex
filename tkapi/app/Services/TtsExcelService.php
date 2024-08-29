<?php

namespace App\Services;

use App\Models\Tt;
use App\Models\TtDay;
use App\Models\TtGroup;
use App\Models\TtGroupType;
use App\Models\TtPart;
use App\Support\Enums\TtGroupTypesEnum as GTYPE;
use Exception;


/**
 * Class TtsExcelService
 * @package App\Services
 */
class TtsExcelService
{
    const KEY = 'lks(;l*jk@';
    const NCOL = 6;
    const YOUNGER_GROUPS_I = 1;
    const OLDER_GROUPS_I = 40;
    const FIRST_PART_ROWS_COUNT = 5;
    const SECOND_PART_ROWS_COUNT = 2;
    const ROWS_FOR_GROUP_COUNT = (self::FIRST_PART_ROWS_COUNT + self::SECOND_PART_ROWS_COUNT) * 5;


    /**
     * Parse collection from Excel
     *
     * @param $data
     * @param $kindergartenId
     * @return null
     * @throws Exception
     */
    public function parse($data, $kindergartenId)
    {
        if (!$data)
            return null;

        $this->validate($data);

        $this->exportToDb($data, $kindergartenId, self::YOUNGER_GROUPS_I);
        $this->exportToDb($data, $kindergartenId, self::OLDER_GROUPS_I);

        return true;
    }

    /**
     * Validate Excel file
     *
     * @param $data
     * @return bool
     * @throws Exception
     */
    private function validate($data)
    {
        if ($data[0][0] != self::KEY)
            throw new Exception('invalid key');

        $groupType1 = $data[self::YOUNGER_GROUPS_I - 1][2];
        $groupType2 = $data[self::OLDER_GROUPS_I - 1][2];
        if ($groupType1 != GTYPE::YOUNGER_TTGROUPTYPE['title_ua'] || $groupType2 != GTYPE::OLDER_TTGROUPTYPE['title_ua'])
            throw new Exception('invalid group type title');

        return true;
    }

    /**
     * Export data to DB
     *
     * @param $data
     * @param $kindergartenId
     * @param $firstI
     */
    private function exportToDb($data, $kindergartenId, $firstI)
    {
        $ttGroupTypes = TtGroupType::get();
        $ttDays = TtDay::get();
        $ttParts = TtPart::get();

        for ($j = 2; $j < self::NCOL; $j++) {
            if ($data[$firstI][$j]) {
                $ttGroup = new TtGroup();
                $ttGroup->makeVisible(['tt_group_type_id', 'kindergarten_id']);

                $ttGroup->title = $data[$firstI][$j];
                $ttGroup->tt_group_type_id = $ttGroupTypes->firstWhere('title_ua', $data[$firstI - 1][2])->id;
                $ttGroup->kindergarten_id = $kindergartenId;
                $ttGroup->save();

                $arr = [];
                $from = $firstI + 1;
                $to = $from + self::ROWS_FOR_GROUP_COUNT;
                for ($i = $from; $i < $to; $i++) {
                    $arr[] = $data[$i][$j];
                }

                $arr = collect($arr);
                $chunks = $arr->chunk(self::FIRST_PART_ROWS_COUNT + self::SECOND_PART_ROWS_COUNT);
                foreach ($chunks as $key=>$chunk) {
                    $chunksParts = $chunk->chunk(self::FIRST_PART_ROWS_COUNT);
                    foreach ($chunksParts as $chunksKey=>$chunksPart) {
                        $chunksPart = $chunksPart->filter(function($p) {return !is_null($p);});

                        $tt = new Tt();
                        $tt->ttGroup()->associate($ttGroup);
                        $tt->ttDay()->associate($ttDays->find($key + 1));
                        $tt->ttPart()->associate($ttParts->find($chunksKey + 1));
                        $tt->subjects = $chunksPart->values();

                        $tt->save();
                    }
                }
            }
        }
    }
}
