<?php

namespace App\Support\Enums;

/**
 * Class NewsGroups
 * @package App\Support\Enums
 */
class NewsGroups
{
    /**
     * Get news groups array
     *
     * @return string[][]
     */
    public static function getNewsGroups()
    {
        return [
            [
                'title' => 'general',
                'title_ru' => 'Общие',
                'title_ua' => 'Загальні'
            ],
            [
                'title' => 'research_activities',
                'title_ru' => 'Исследовательская деятельность',
                'title_ua' => 'Дослідницька діяльність'
            ],
            [
                'title' => 'patriotic_education',
                'title_ru' => 'Патриотическое воспитание',
                'title_ua' => 'Патріотичне виховання'
            ],
            [
                'title' => 'publications_on_web',
                'title_ru' => 'Публикации на веб ресурсах',
                'title_ua' => 'Публікації на веб ресурсах'
            ],
        ];
    }
}
