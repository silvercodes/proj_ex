<?php

namespace App\Support\Rules;

/**
 * Class ValidationRules
 * @package App\Support\Rules
 */
class ValidationRules
{
    /**
     * Get array of rules by rule name
     *
     * @param string $rule
     * @return array
     */
    public static function get(string $rule) : array
    {
        return ValidationRules::$rules[$rule];
    }

    /**
     * Validation rules
     *
     * @var array
     */
    private static $rules = [
        'signin' => [
            'email'                 => 'required|email',
            'password'              => 'required'
        ],

        'create_album' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'title'                 => 'required|string',
            'description'           => 'sometimes|string',
        ],

        'get_all_albums_by_kindergarten_id' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
        ],

        'photos_multiple_upload' => [
            'upload'                => 'required',
            'upload.*'              => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240'
        ],

        'patch_album' => [
            'title'                 => 'sometimes|required|string',
            'description'           => 'sometimes|string|nullable'
        ],

        // DOCUMENTS
        'create_document'           => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'document_group_id'     => 'required|numeric|exists:App\Models\DocumentGroup,id',
            'title'                 => 'string',
            'description'           => 'string',
            'external_link'         => 'url',
            'attached_file'         => 'file|max:10240',
        ],

        'documents_multiple_upload' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'document_group_id'     => 'required|numeric|exists:App\Models\DocumentGroup,id',
            'upload.*'              => 'file|max:10240'
        ],

        'get_documents' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'filter'                => 'sometimes|required|string|in:openness,method_case',
        ],

        // EMPLOYEES
        'add_employee' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'kindergarten_group_id' => 'sometimes|required|numeric|exists:App\Models\KindergartenGroup,id',
            'full_name'             => 'required|string|max:100',
            'position'              => 'required|string|max:100',
            'education'             => 'string',
            'teaching_experience'   => 'numeric',
            'management_experience' => 'numeric',
            'awards'                => 'string',
            'is_administration'     => 'sometimes|required|boolean',
            'employee_photo'        => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240'
        ],

        'get_employees_by_kindergarten_id' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'filter'                => 'sometimes|required|string|in:administration,educator'
        ],

        'patch_employee' => [
            'kindergarten_group_id' => 'sometimes|required|numeric|exists:App\Models\KindergartenGroup,id',
            'full_name'             => 'sometimes|required|string|max:100',
            'position'              => 'sometimes|required|string|max:100',
            'education'             => 'string',
            'teaching_experience'   => 'numeric',
            'management_experience' => 'numeric',
            'awards'                => 'string',
            'is_administration'     => 'sometimes|required|boolean',
            'employee_photo'        => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            '_method'               => 'required|string|in:PATCH',
        ],

        'create_kindergarten_group' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'title'                 => 'required|string',
            'images'                => 'sometimes|required',
            'images.*'              => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240'
        ],

        'get_kindergarten_groups_by_kindergarten_id' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
        ],

        'patch_kindergarten_group' => [
            'title'                 => 'sometimes|required|string',
            'images'                => 'sometimes|required',
            'images.*'              => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            '_method'               => 'required|string|in:PATCH',
        ],

        //TT
        'create_ttgroup' => [
            'title'                 => 'required|string',
            'tt_group_type_id'      => 'required|numeric|exists:App\Models\TtGroupType,id',
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
        ],

        'create_tt' => [
            'tt_group_id'           => 'required|numeric|exists:App\Models\TtGroup,id',
            'tt_day_id'             => 'required|numeric|exists:App\Models\TtDay,id',
            'tt_part_id'            => 'required|numeric|exists:App\Models\TtPart,id',
            'subjects'              => 'required',
            'subjects.*'            => 'string'
        ],

        'get_tts_by_kindergarten_id' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
        ],

        'upload_tts' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
        ],

        //NEWS
        'create_news' => [
            'title'                 => 'required|string',
            'description'           => 'sometimes|string|nullable',
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'news_group_id'         => 'required|numeric|exists:App\Models\NewsGroup,id',
            'news_image'            => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'album_id'              => 'sometimes|required|numeric|exists:App\Models\Album,id'
        ],

        'patch_news' => [
            'title'                 => 'sometimes|required|string',
            'description'           => 'sometimes|string|nullable',
            'news_image'            => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'album_id'              => 'sometimes|required|numeric|exists:App\Models\Album,id',
            '_method'               => 'required|string|in:PATCH',
        ],

        'get_news_by_kindergarten_id' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'filter'                => 'sometimes|required|string|in:general,research_activities,patriotic_education,publications_on_web'
        ],

        // WELCOME BLOCKS
        'create_welcome_block' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
            'title'                 => 'required|string',
            'text'                  => 'string',
            'image'                 => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240'
        ],

        'get_welcome_block' => [
            'kindergarten_id'       => 'required|numeric|exists:App\Models\Kindergarten,id',
        ],

        'patch_welcome_block' => [
            'title'                 => 'sometimes|required|string',
            'text'                  => 'string',
            'image'                 => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240'
        ],
    ];

}
