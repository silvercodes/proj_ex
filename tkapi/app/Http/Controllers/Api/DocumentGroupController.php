<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Models\DocumentGroup;

/**
 * Class DocumentGroupController
 * @package App\Http\Controllers\Api
 */
class DocumentGroupController extends ApiController
{
    /**
     * Get all documents groups
     *
     * @return JsonResponse
     */
    public function getAll():JsonResponse
    {
        $documentsGroups = DocumentGroup::get();

        return $this->buildRes(200, $documentsGroups);
    }
}
