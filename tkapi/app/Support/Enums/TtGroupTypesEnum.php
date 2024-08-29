<?php

namespace App\Support\Enums;

use ReflectionClass;

/**
 * Class PermissionsEnum
 * @package App\Support\Enums
 */
class TtGroupTypesEnum {
    /**
     * Get constants array
     *
     * @return array
     */
    public static function getConstants(): array
    {
        $rClass = new ReflectionClass(__CLASS__);
        return $rClass->getConstants();
    }

    const YOUNGER_TTGROUPTYPE = [
        'title' => 'younger_group_type',
        'title_ru' => 'Младшая группа',
        'title_ua' => 'Молодша група',
    ];

    const OLDER_TTGROUPTYPE = [
        'title' => 'older_group_type',
        'title_ru' => 'Старшая группа',
        'title_ua' => 'Старша група',
    ];

}
