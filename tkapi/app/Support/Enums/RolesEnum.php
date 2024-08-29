<?php

namespace App\Support\Enums;

use ReflectionClass;
use App\Support\Enums\PermissionsEnum as PERM;

/**
 * Class RolesEnum
 * @package App\Support\Enums
 */
class RolesEnum {
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

    const SUPER_ADMIN = [
        'title' => 'Super-admin',
        'slug' => 'super-admin',
        'permissions' => [
            PERM::CREATE_ANY_ALBUM,
            PERM::UPLOAD_PHOTO_TO_ANY_ALBUM,
            PERM::PATCH_ANY_ALBUM,
            PERM::DELETE_ANY_ALBUM,
            PERM::UPLOAD_ANY_DOCUMENTS,
            PERM::DELETE_ANY_DOCUMENTS,
            PERM::ADD_ANY_EMPLOYEE,
            PERM::DELETE_ANY_EMPLOYEE,
            PERM::CREATE_ANY_KINDERGARTEN_GROUP,
            PERM::DELETE_ANY_KINDERGARTEN_GROUP,
            PERM::PATCH_ANY_KINDERGARTEN_GROUP,
            PERM::PATCH_ANY_EMPLOYEE,
            PERM::CREATE_ANY_TTGROUP,
            PERM::CREATE_ANY_TT,
            PERM::CREATE_ANY_NEWS,
            PERM::PATCH_ANY_NEWS,
            PERM::DELETE_ANY_NEWS,
            PERM::CREATE_ANY_WELCOME_BLOCK,
            PERM::PATCH_ANY_WELCOME_BLOCK,
        ]
    ];

    const ADMIN = [
        'title' => 'Admin',
        'slug' => 'admin',
        'permissions' => [
            PERM::CREATE_OWN_ALBUM,
            PERM::UPLOAD_PHOTO_TO_OWN_ALBUM,
            PERM::PATCH_OWN_ALBUM,
            PERM::DELETE_OWN_ALBUM,
            PERM::UPLOAD_OWN_DOCUMENTS,
            PERM::DELETE_OWN_DOCUMENTS,
            PERM::ADD_OWN_EMPLOYEE,
            PERM::DELETE_OWN_EMPLOYEE,
            PERM::CREATE_OWN_KINDERGARTEN_GROUP,
            PERM::DELETE_OWN_KINDERGARTEN_GROUP,
            PERM::PATCH_OWN_KINDERGARTEN_GROUP,
            PERM::PATCH_OWN_EMPLOYEE,
            PERM::CREATE_OWN_TTGROUP,
            PERM::CREATE_OWN_TT,
            PERM::CREATE_OWN_NEWS,
            PERM::PATCH_OWN_NEWS,
            PERM::DELETE_OWN_NEWS,
            PERM::CREATE_OWN_WELCOME_BLOCK,
            PERM::PATCH_OWN_WELCOME_BLOCK,
        ]
    ];
}
