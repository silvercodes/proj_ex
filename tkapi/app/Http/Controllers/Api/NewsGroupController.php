<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\ApiController;
use App\Models\NewsGroup;

/**
 * Class NewsGroupController
 * @package App\Http\Controllers\Api
 */
class NewsGroupController extends ApiController
{
    /**
     * Get all news groups
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $newsGroups = NewsGroup::get();

        return $this->buildRes(200, $newsGroups);
    }
}
