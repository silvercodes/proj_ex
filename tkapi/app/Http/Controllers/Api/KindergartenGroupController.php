<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use DB;
use Exception;
use App\Support\Enums\PermissionsEnum as PERM;
use App\Support\Enums\FileTypesEnum as FT;
use App\Services\FileService;
use App\Services\ValidationService;
use App\Models\Kindergarten;
use App\Models\KindergartenGroup;


/**
 * Class KindergartenGroupController
 * @package App\Http\Controllers\Api
 */
class KindergartenGroupController extends ApiController
{
    /**
     * Create new kindergarten group
     *
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function create(ValidationService $validationService, FileService $fileService): JsonResponse
    {
        $validationErrors = $validationService->check('create_kindergarten_group');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $user = Auth::user();
        $kindergarten = Kindergarten::find(request()->kindergarten_id);

        if (!($user->can(PERM::CREATE_ANY_KINDERGARTEN_GROUP['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::CREATE_OWN_KINDERGARTEN_GROUP['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $kindergartenGroup = new KindergartenGroup();
        $kindergartenGroup->title = request()->title;
        $kindergartenGroup->kindergarten()->associate($kindergarten);

        $kindergartenGroup->save();

        if (request()->images) {
            foreach (request()->images as $image) {
                $file = $fileService->save($image, FT::KINDERGARTEN_GROUP_IMAGE_FILE_TYPE['slug']);

                if ($file) {
                    $kindergartenGroup->files()->save($file);
                }
            }
        }

        $kindergartenGroup->load([
            'employees'
        ]);

        return $this->buildRes(200, $kindergartenGroup);
    }


    /**
     * Get kindergarten groups by kindergarten id
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function getByKindergartenId(ValidationService $validationService)
    {
        $validationErrors = $validationService->check('get_kindergarten_groups_by_kindergarten_id');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $kindergarten = Kindergarten::find(request()->kindergarten_id);
        $kindergartenGroups = $kindergarten->kindergartenGroups;

        return $this->buildRes(200, $kindergartenGroups);
    }

    /**
     * Get kindergarten group by id
     *
     * @param $kindergartenGroupId
     * @return JsonResponse
     */
    public function getById($kindergartenGroupId): JsonResponse
    {
        if (!is_numeric($kindergartenGroupId) || !$kindergartenGroup = KindergartenGroup::find($kindergartenGroupId))
            return $this->buildRes(404);

        return $this->buildRes(200, $kindergartenGroup);
    }

    /**
     * Delete kindergarten group
     *
     * @param $kindergartenGroupId
     * @param FileService $fileService
     * @return JsonResponse
     * @throws Exception
     */
    public function delete($kindergartenGroupId, FileService $fileService)
    {
        if (!is_numeric($kindergartenGroupId) || !$kindergartenGroup = KindergartenGroup::find($kindergartenGroupId))
            return $this->buildRes(404);

        $user = Auth::user();

        if (!($user->can(PERM::DELETE_ANY_KINDERGARTEN_GROUP['slug']) ||
            ($user->id === $kindergartenGroup->kindergarten->user_id && $user->can(PERM::DELETE_OWN_KINDERGARTEN_GROUP['slug'])))
        ) {
            return $this->buildRes(403);
        }

        foreach ($kindergartenGroup->employees as $employee) {
            $employee->kindergarten_group_id = null;
            $employee->save();
        }

        $files = $kindergartenGroup->files;
        foreach ($files as $file) {
            $fileService->delete($file);
        }

        // delete relationships
        DB::table('kindergarten_groups_files')
            ->where('kindergarten_group_id', '=', $kindergartenGroup->id)
            ->delete();

        $kindergartenGroup->delete();

        return $this->buildRes(200);
    }

    /**
     * Patch kindergarten group
     *
     * @param string $kindergartenGroupId
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function patch(
        string $kindergartenGroupId,
        ValidationService $validationService,
        FileService $fileService
    ): JsonResponse {
        if (!is_numeric($kindergartenGroupId) || !$kindergartenGroup = KindergartenGroup::find($kindergartenGroupId))
            return $this->buildRes(404);

        $user = Auth::user();

        if (!($user->can(PERM::PATCH_ANY_KINDERGARTEN_GROUP['slug']) ||
            ($user->id === $kindergartenGroup->kindergarten->user_id && $user->can(PERM::PATCH_OWN_KINDERGARTEN_GROUP['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $validationErrors = $validationService->check('patch_kindergarten_group');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $kindergartenGroup->update(request()->only([
            'title',
        ]));

        if (request()->images) {
            foreach (request()->images as $image) {
                $file = $fileService->save($image, FT::KINDERGARTEN_GROUP_IMAGE_FILE_TYPE['slug']);

                if ($file) {
                    $kindergartenGroup->files()->save($file);
                }
            }
        }

        return $this->buildRes(200, $kindergartenGroup);
    }
}
