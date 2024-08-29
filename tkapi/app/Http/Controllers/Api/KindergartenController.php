<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Kindergarten;
use Illuminate\Http\JsonResponse;

/**
 * Class KindergartenController
 * @package App\Http\Controllers\Api
 */
class KindergartenController extends ApiController
{
    /**
     * Get all kindergartens
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $kindergartens = Kindergarten::get();

        return $this->buildRes(200, $kindergartens);
    }

    /**
     * Get kindergarten by id
     *
     * @param $id
     * @return JsonResponse
     */
    public function getById($id): JsonResponse
    {
        if (!is_numeric($id) || !Kindergarten::find($id))
            return $this->buildRes(404);

        $kindergarten = Kindergarten::find($id);

        $kindergarten->load([
            'user',
            'albums',
        ]);

        return $this->buildRes(200, $kindergarten);
    }
}
