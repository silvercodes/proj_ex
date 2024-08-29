<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use Exception;
use App\Services\FileService;
use App\Services\ValidationService;
use App\Support\Enums\PermissionsEnum as PERM;
use App\Support\Enums\FileTypesEnum as FT;
use App\Models\Kindergarten;
use App\Models\News;
use App\Models\Album;
use App\Models\NewsGroup;


/**
 * Class NewsController
 * @package App\Http\Controllers\Api
 */
class NewsController extends ApiController
{
    /**
     * Create news
     *
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function create(ValidationService $validationService, FileService $fileService): JsonResponse
    {
        $validationErrors = $validationService->check('create_news');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $user = Auth::user();
        $kindergarten = Kindergarten::find(request()->kindergarten_id);

        if ($user->can(PERM::CREATE_ANY_NEWS['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::CREATE_OWN_NEWS['slug']))
        ) {
            $news = new News();
            $news->title = request()->title;
            $news->description = request()->description;
            $news->kindergarten()->associate($kindergarten);
            $news->newsGroup()->associate(NewsGroup::find(request()->news_group_id));

            if (request()->album_id && $album = Album::find(request()->album_id)) {
                if ($album->kindergarten_id == $kindergarten->id)

                    $news->album_id = request()->album_id;
            }

            if (request()->news_image) {
                $file = $fileService->save(request()->news_image, FT::NEWS_IMAGE_FILE_TYPE['slug']);

                if ($file)
                    $news->file_id = $file->id;
            }

            $news->save();

            $news->load('album');

        } else {
            return $this->buildRes(403);
        }

        return $this->buildRes(200, $news);
    }

    /**
     * Patch news
     *
     * @param string $newsId
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function patch(
        string $newsId,
        ValidationService $validationService,
        FileService $fileService
    ): JsonResponse {
        if (!is_numeric($newsId) || !$news = News::find($newsId))
            return $this->buildRes(404);

        $user = Auth::user();

        if (!($user->can(PERM::PATCH_ANY_NEWS['slug']) ||
            ($user->id === $news->kindergarten->user_id && $user->can(PERM::PATCH_OWN_NEWS['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $validationErrors = $validationService->check('patch_news');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $news->update(request()->only([
            'title',
            'description'
        ]));

        // News group
        if (request()->album_id && $album = Album::find(request()->album_id)) {
            if ($album->kindergarten_id == $news->kindergarten->id) {
                $news->album_id = request()->album_id;
                $news->save();
            }
        }

        // Image
        if (request()->news_image) {
            $oldFile = $news->file;

            $file = $fileService->save(request()->news_image, FT::NEWS_IMAGE_FILE_TYPE['slug']);

            if ($file) {
                $news->file_id = $file->id;
                if($oldFile)
                    $fileService->delete($oldFile);
                $news->save();
            }
        }

        return $this->buildRes(200, News::find($newsId)->load('album'));
    }

    /**
     * Get news by kindergarten id
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function getByKindergartenId(ValidationService $validationService)
    {
        $validationErrors = $validationService->check('get_news_by_kindergarten_id');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $news = News::where('kindergarten_id', request()->kindergarten_id);

        $filter = request()->filter ?? 'general'; // TODO: hardcode
        $newsGroup = NewsGroup::where('title', $filter)->first();

        $news = $news->where('news_group_id', '=', $newsGroup->id)
            ->with('album')
            ->get();

        return $this->buildRes(200, $news);
    }

    /**
     * Get news by id
     *
     * @param string $newsId
     * @return JsonResponse
     */
    public function getById(string $newsId)
    {
        $news = News::find($newsId)->load('album', 'newsGroup');

        return $this->buildRes(200, $news);
    }

    /**
     * Delete news
     *
     * @param string $newsId
     * @param FileService $fileService
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(string $newsId, FileService $fileService)
    {
        if (!is_numeric($newsId) || !$news = News::find($newsId))
            return $this->buildRes(404);

        $user = Auth::user();

        if (!($user->can(PERM::DELETE_ANY_NEWS['slug']) ||
            ($user->id === $news->kindergarten->user_id && $user->can(PERM::DELETE_OWN_NEWS['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $newsImage = $news->file;
        if ($newsImage)
            $fileService->delete($newsImage);

        $news->delete();

        return $this->buildRes(200);
    }
}
