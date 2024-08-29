<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use App\Services\ValidationService;
use App\Support\Enums\PermissionsEnum as PERM;
use App\Models\Kindergarten;
use App\Models\TtGroup;
use App\Models\TtGroupType;


/**
 * Class TtGroupController
 * @package App\Http\Controllers\Api
 */
class TtGroupController extends ApiController
{
    /**
     * Create ttGroup
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function create(ValidationService $validationService): JsonResponse
    {
        $validationErrors = $validationService->check('create_ttgroup');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $user = Auth::user();
        $kindergarten = Kindergarten::find(request()->kindergarten_id);

        if (!($user->can(PERM::CREATE_ANY_TTGROUP['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::CREATE_OWN_TTGROUP['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $ttGroupType = TtGroupType::find(request()->tt_group_type_id);

        $ttGroup = new TtGroup();
        $ttGroup->title = request()->title;
        $ttGroup->ttGroupType()->associate($ttGroupType);
        $ttGroup->kindergarten()->associate($kindergarten);

        $ttGroup->save();

        return $this->buildRes(200, $ttGroup);
    }
}
