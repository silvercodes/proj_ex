<?php

namespace App\Support\Enums;

use ReflectionClass;

/**
 * Class PermissionsEnum
 * @package App\Support\Enums
 */
class TtPartsEnum
{
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

    const FIRST_TTPART = [
        'title' => 'first_ttpart',
        'title_ru' => 'I половина дня',
        'title_ua' => 'I половина дня',
    ];

    const SECOND_TTPART = [
        'title' => 'second_ttpart',
        'title_ru' => 'II половина дня',
        'title_ua' => 'II половина дня',
    ];
}
