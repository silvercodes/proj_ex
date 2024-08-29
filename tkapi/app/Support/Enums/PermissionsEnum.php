<?php

namespace App\Support\Enums;

use ReflectionClass;

/**
 * Class PermissionsEnum
 * @package App\Support\Enums
 */
class PermissionsEnum {
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

    const CREATE_ANY_ALBUM = [
        'title' => 'create any album',
        'slug' => 'create-any-album'
    ];

    const CREATE_OWN_ALBUM = [
        'title' => 'create own album',
        'slug' => 'create-own-album'
    ];

    const UPLOAD_PHOTO_TO_ANY_ALBUM = [
        'title' => 'upload photo to any album',
        'slug' => 'upload-photo-to-any-album'
    ];

    const UPLOAD_PHOTO_TO_OWN_ALBUM = [
        'title' => 'upload photo to own album',
        'slug' => 'upload-photo-to-own-album'
    ];

    const PATCH_ANY_ALBUM = [
        'title' => 'patch any album',
        'slug' => 'patch-any-album',
    ];

    const PATCH_OWN_ALBUM = [
        'title' => 'patch own album',
        'slug' => 'patch-own-album',
    ];

    const DELETE_ANY_ALBUM = [
        'title' => 'delete any album',
        'slug' => 'delete-any-album',
    ];

    const DELETE_OWN_ALBUM = [
        'title' => 'delete own album',
        'slug' => 'delete-own-album',
    ];

    const UPLOAD_ANY_DOCUMENTS = [
        'title' => 'upload any documents',
        'slug' => 'upload-any-documents',
    ];

    const UPLOAD_OWN_DOCUMENTS = [
        'title' => 'upload own documents',
        'slug' => 'upload-own-documents',
    ];

    const DELETE_ANY_DOCUMENTS = [
        'title' => 'delete any documents',
        'slug' => 'delete-any-documents',
    ];

    const DELETE_OWN_DOCUMENTS = [
        'title' => 'delete own documents',
        'slug' => 'delete-own-documents',
    ];

    const ADD_ANY_EMPLOYEE = [
        'title' => 'add any employee',
        'slug' => 'add-any-employee'
    ];

    const ADD_OWN_EMPLOYEE = [
        'title' => 'add own employee',
        'slug' => 'add-own-employee'
    ];

    const DELETE_ANY_EMPLOYEE = [
        'title' => 'delete any employee',
        'slug' => 'delete-any-employee'
    ];

    const DELETE_OWN_EMPLOYEE = [
        'title' => 'delete own employee',
        'slug' => 'delete-own-employee'
    ];

    const PATCH_ANY_EMPLOYEE = [
        'title' => 'patch any employee',
        'slug' => 'patch-any-employee'
    ];

    const PATCH_OWN_EMPLOYEE = [
        'title' => 'patch own employee',
        'slug' => 'patch-own-employee'
    ];

    const CREATE_ANY_KINDERGARTEN_GROUP = [
        'title' => 'create any kindergarten group',
        'slug' => 'create-any-kindergarten-group'
    ];

    const CREATE_OWN_KINDERGARTEN_GROUP = [
        'title' => 'create own kindergarten group',
        'slug' => 'create-own-kindergarten-group'
    ];

    const DELETE_ANY_KINDERGARTEN_GROUP = [
        'title' => 'delete any kindergarten group',
        'slug' => 'delete-any-kindergarten-group'
    ];

    const DELETE_OWN_KINDERGARTEN_GROUP = [
        'title' => 'delete own kindergarten group',
        'slug' => 'delete-own-kindergarten-group'
    ];

    const PATCH_ANY_KINDERGARTEN_GROUP = [
        'title' => 'patch any kindergarten group',
        'slug' => 'patch-any-kindergarten-group',
    ];

    const PATCH_OWN_KINDERGARTEN_GROUP = [
        'title' => 'patch own kindergarten group',
        'slug' => 'patch-own-kindergarten-group',
    ];

    // TT
    const CREATE_ANY_TTGROUP = [
        'title' => 'create any tt group',
        'slug' => 'create-any-tt-group'
    ];

    const CREATE_OWN_TTGROUP = [
        'title' => 'create own tt group',
        'slug' => 'create-own-tt-group'
    ];

    const CREATE_ANY_TT = [
        'title' => 'create any tt',
        'slug' => 'create-any-tt'
    ];

    const CREATE_OWN_TT = [
        'title' => 'create own tt',
        'slug' => 'create-own-tt'
    ];

    // NEWS
    const CREATE_ANY_NEWS = [
        'title' => 'create any news',
        'slug' => 'create-any-news'
    ];

    const CREATE_OWN_NEWS = [
        'title' => 'create own news',
        'slug' => 'create-own-news'
    ];

    const PATCH_ANY_NEWS = [
        'title' => 'patch any news',
        'slug' => 'patch-any-news'
    ];

    const PATCH_OWN_NEWS = [
        'title' => 'patch own news',
        'slug' => 'patch-own-news'
    ];

    const DELETE_ANY_NEWS = [
        'title' => 'delete any news',
        'slug' => 'delete-any-news'
    ];

    const DELETE_OWN_NEWS = [
        'title' => 'delete own news',
        'slug' => 'delete-own-news'
    ];

    // WELCOME BLOCKS
    const CREATE_ANY_WELCOME_BLOCK = [
        'title' => 'create any welcome block',
        'slug' => 'create-any-welcome-block'
    ];

    const CREATE_OWN_WELCOME_BLOCK = [
        'title' => 'create own welcome block',
        'slug' => 'create-own-welcome-block'
    ];

    const PATCH_ANY_WELCOME_BLOCK = [
        'title' => 'patch any welcome block',
        'slug' => 'patch-any-welcome-block'
    ];

    const PATCH_OWN_WELCOME_BLOCK = [
        'title' => 'patch own welcome block',
        'slug' => 'patch-own-welcome-block'
    ];
}
