<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;


/**
 * Class ApiController
 *
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    /**
     * @param integer $status
     * @param array $results
     * @param array $errors
     * @return JsonResponse
     */
    protected function buildRes($status, $results = [], $errors = []): JsonResponse
    {
        $response = [
            'errors' => $errors,
            'results' => $results,
        ];

        return response()->json($response, $status);
    }
}
