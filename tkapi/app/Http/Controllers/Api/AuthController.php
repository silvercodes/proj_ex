<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Services\AuthService;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;


/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 */
class AuthController extends ApiController
{
    /**
     * @param ValidationService $validator
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function signin(
        ValidationService $validator,
        AuthService $authService
    ): JsonResponse
    {
        $errors = $validator->check('signin');

        if ($errors)
            return $this->buildRes(400, [], $errors);

        $authResult = $authService->authorize(request()->all());

        if (!$authResult)
            return $this->buildRes(401);

        return $this->buildRes(200, $authResult);
    }
}
