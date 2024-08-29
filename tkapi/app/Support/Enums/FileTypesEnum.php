<?php

namespace App\Support\Enums;

use ReflectionClass;

/**
 * Class FileTypesEnum
 * @package App\Support\Enums
 */
class FileTypesEnum {
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

    const PHOTO_FILE_TYPE = [
        'slug' => 'photo-file-type',
        'dir' => 'photos',
    ];

    const DOCUMENT_FILE_TYPE = [
        'slug' => 'document-file-type',
        'dir' => 'documents',
    ];

    const EMPLOYEE_PHOTO_FILE_TYPE = [
        'slug' => 'employee-photo-file-type',
        'dir' => 'employeesPhotos'
    ];

    const KINDERGARTEN_GROUP_IMAGE_FILE_TYPE = [
        'slug' => 'kindergartens-groups-image-file-type',
        'dir' => 'kindergartensGroupsImages'
    ];

    const NEWS_IMAGE_FILE_TYPE = [
        'slug' => 'news-image-file-type',
        'dir' => 'newsImages'
    ];

    const WELCOME_BLOCK_FILE_TYPE = [
        'slug' => 'welcome-block-file-type',
        'dir' => 'welcomeBlockImages'
    ];

}
