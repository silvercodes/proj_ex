<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\FileService;
use App\Services\ValidationService;
use App\Support\Enums\FileTypesEnum as FT;
use App\Support\Enums\PermissionsEnum as PERM;
use App\Models\Kindergarten;
use App\Models\WelcomeBlock;

/**
 * Class WelcomeBlockController
 * @package App\Http\Controllers\Api
 */
class WelcomeBlockController extends ApiController
{
    /**
     * Remove all existing wb by kindergarten id
     *
     * @param $kindergartenId
     * @param FileService $fileService
     * @return void
     */
    private function removeWB($kindergartenId, FileService $fileService): void
    {
        $wbs = WelcomeBlock::where('kindergarten_id', '=', $kindergartenId)
            ->get();

        if (!$wbs)
            return;

        foreach($wbs as $wb) {
            if ($wbFile = $wb->file)
                $fileService->delete($wbFile);
            $wb->delete();
        }
    }

    /**
     * Create welcome block
     *
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function create(ValidationService $validationService, FileService $fileService): JsonResponse
    {
        $validationErrors = $validationService->check('create_welcome_block');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $kindergarten = Kindergarten::find(request()->kindergarten_id);

        $user = Auth::user();

        if ($user->can(PERM::CREATE_ANY_WELCOME_BLOCK['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::CREATE_OWN_WELCOME_BLOCK['slug']))
        ) {
            $this->removeWB(request()->kindergarten_id, $fileService);

            $wb = new WelcomeBlock();
            $wb->title = request()->title;
            $wb->text = request()->text;
            $wb->kindergarten_id = request()->kindergarten_id;

            if (request()->image) {
                $file = $fileService->save(request()->image, FT::WELCOME_BLOCK_FILE_TYPE['slug']);

                if ($file)
                    $wb->file_id = $file->id;
            }

            $wb->save();
        } else {
            return $this->buildRes(403);
        }

        return $this->buildRes(200, $wb);
    }

    /**
     * Patch welcome block
     *
     * @param string $welcomeBlockId
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function patch(
        string $welcomeBlockId,
        ValidationService $validationService,
        FileService $fileService
    ) {
        if (!is_numeric($welcomeBlockId) || !$wb = WelcomeBlock::find($welcomeBlockId))
            return $this->buildRes(404);

        $user = Auth::user();
        $kindergarten = Kindergarten::find($wb->kindergarten_id);

        if (!($user->can(PERM::PATCH_ANY_WELCOME_BLOCK['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::PATCH_OWN_WELCOME_BLOCK['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $validationErrors = $validationService->check('patch_welcome_block');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $wb->update(request()->only([
            'title',
            'text',
        ]));

        // File
        if (request()->image) {
            $oldFile = $wb->file;

            $file = $fileService->save(request()->image, FT::WELCOME_BLOCK_FILE_TYPE['slug']);

            if ($file) {
                $wb->file_id = $file->id;
                if($oldFile)
                    $fileService->delete($oldFile);
                $wb->save();
            }
        }

        return $this->buildRes(200, WelcomeBlock::find($welcomeBlockId));
    }

    /**
     * Get welcome block by kindergarten id
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function getByKindergartenId(ValidationService $validationService): JsonResponse
    {
        $validationErrors = $validationService->check('get_welcome_block');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $wb = WelcomeBlock::where('kindergarten_id', '=', request()->kindergarten_id)
            ->first();

        return $this->buildRes(200, $wb);
    }
}
