<?php

namespace App\Support\Enums;

use ReflectionClass;

/**
 * Class PermissionsEnum
 * @package App\Support\Enums
 */
class TtDaysEnum {
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

    const MON_TTDAY = [
        'title' => 'monday',
        'title_ru' => 'понедельник',
        'title_ua' => 'понедiлок',
    ];

    const TUE_TTDAY = [
        'title' => 'tuesday',
        'title_ru' => 'вторник',
        'title_ua' => 'вiвторок',
    ];

    const WED_TTDAY = [
        'title' => 'wednesday',
        'title_ru' => 'среда',
        'title_ua' => 'середа',
    ];

    const THU_TTDAY = [
        'title' => 'thursday',
        'title_ru' => 'четверг',
        'title_ua' => 'четвер',
    ];

    const FRI_TTDAY = [
        'title' => 'friday',
        'title_ru' => 'пятница',
        'title_ua' => 'п\'ятниця',
    ];

    const SAT_TTDAY = [
        'title' => 'saturday',
        'title_ru' => 'суббота',
        'title_ua' => 'субота',
    ];

    const SUN_TTDAY = [
        'title' => 'sunday',
        'title_ru' => 'воскресенье',
        'title_ua' => 'недiля',
    ];
}
