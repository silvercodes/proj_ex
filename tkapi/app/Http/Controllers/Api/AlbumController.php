<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Controllers\ApiController;
use App\Support\Enums\PermissionsEnum as PERM;
use App\Support\Enums\FileTypesEnum as FT;
use App\Services\ValidationService;
use App\Services\FileService;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Kindergarten;

/**
 * Class AlbumController
 * @package App\Http\Controllers\Api
 */
class AlbumController extends ApiController
{
    /**
     * Get all albums by kindergarten_id
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function getAllByKindergartenId(ValidationService $validationService): JsonResponse
    {
        $validationErrors = $validationService->check('get_all_albums_by_kindergarten_id');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $albums = Album::where('kindergarten_id', request()->kindergarten_id)
            ->with('photos')
            ->get();

        return $this->buildRes(200, $albums);
    }
    /**
     * Get album by id with photos
     *
     * @param string $albumId
     * @return JsonResponse
     */
    public function getById(string $albumId): JsonResponse
    {
        if (!is_numeric($albumId) || !Album::find($albumId))
            return $this->buildRes(404);

        $album = Album::find($albumId)->load([
            'photos',
        ]);

        return $this->buildRes(200, $album);
    }

    /**
     * Create a new album
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function create(ValidationService $validationService): JsonResponse
    {
        $validationErrors = $validationService->check('create_album');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $kindergarten = Kindergarten::find(request()->kindergarten_id);

        $user = Auth::user();

        if ($user->can(PERM::CREATE_ANY_ALBUM['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::CREATE_OWN_ALBUM['slug']))
        ) {
            $album = new Album();
            $album->title = request()->title;
            $album->description = request()->description ?? null;
            $album->kindergarten()->associate($kindergarten);

            $album->save();
        } else {
            return $this->buildRes(403);
        }

        return $this->buildRes(200, $album);
    }

    /**
     * Upload a few photos to the album
     *
     * @param string $albumId
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function multipleUpload(
        string $albumId,
        ValidationService $validationService,
        FileService $fileService
    ): JsonResponse {
        if (!is_numeric($albumId) || !Album::find($albumId))
            return $this->buildRes(404);

        $validationErrors = $validationService->check('photos_multiple_upload');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $album = Album::find($albumId);

        $user = Auth::user();

        if (!($user->can(PERM::UPLOAD_PHOTO_TO_ANY_ALBUM['slug']) ||
            ($user->id === $album->kindergarten->user_id && $user->can(PERM::UPLOAD_PHOTO_TO_OWN_ALBUM['slug'])))
        ) {
            return $this->buildRes(403);
        }

        foreach(request()->upload as $f) {
            $file = $fileService->save($f, FT::PHOTO_FILE_TYPE['slug']);

            if ($file) {
                $photo = new Photo();
                $photo->file_id = $file->id;
                $photo->album()->associate($album);
                $photo->save();
            }
        }

        $album->load(['photos']);

        return $this->buildRes(200, $album);
    }

    /**
     * Patch album
     *
     * @param string $albumId
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function patch(string $albumId, ValidationService $validationService): JsonResponse
    {
        if (!is_numeric($albumId) || !$album = Album::find($albumId))
            return $this->buildRes(404);

        $user = Auth::user();

        if (!($user->can(PERM::PATCH_ANY_ALBUM['slug']) ||
            ($user->id === $album->kindergarten->user_id && $user->can(PERM::PATCH_OWN_ALBUM['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $validationErrors = $validationService->check('patch_album');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $album->update(request()->only(['title', 'description']));

        return $this->buildRes(200, $album);
    }

    /**
     * Delete album
     *
     * @param string $albumId
     * @param FileService $fileService
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(string $albumId, FileService $fileService): JsonResponse
    {
        if (!is_numeric($albumId) || !$album = Album::find($albumId))
            return $this->buildRes(404);

        $user = Auth::user();

        if (!($user->can(PERM::DELETE_ANY_ALBUM['slug']) ||
            ($user->id === $album->kindergarten->user_id && $user->can(PERM::DELETE_OWN_ALBUM['slug'])))
        ) {
            return $this->buildRes(403);
        }

        foreach ($album->photos as $photo) {
            $fileService->delete($photo->file);
            $photo->delete();
        }

        $album->delete();

        return $this->buildRes(200);
    }
}
