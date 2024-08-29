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
use App\Models\Document;
use App\Models\DocumentGroup;
use App\Models\Kindergarten;


/**
 * Class DocumentController
 * @package App\Http\Controllers\Api
 */
class DocumentController extends ApiController
{
    /**
     * Create single document
     *
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function create(
        ValidationService $validationService,
        FileService $fileService
    ): JsonResponse {
        $validationErrors = $validationService->check('create_document');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $user = Auth::user();
        $kindergarten = Kindergarten::find(request()->kindergarten_id);
        $documentGroup = DocumentGroup::find(request()->document_group_id);

        if (!($user->can(PERM::UPLOAD_ANY_DOCUMENTS['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::UPLOAD_OWN_DOCUMENTS['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $document = new Document();
        $document->kindergarten()->associate($kindergarten);
        $document->documentGroup()->associate(($documentGroup));
        $document->title = request()->title ?? null;
        $document->description = request()->description ?? null;
        $document->external_link = request()->external_link ?? null;
        if (request()->attached_file) {
            if ($file = $fileService->save(request()->attached_file, FT::DOCUMENT_FILE_TYPE['slug'])) {
                $document->file_id = $file->id;
            }
        }

        $document->save();

        return $this->buildRes(200, $document);
    }

    /**
     * Upload a few documents for specific kindergarten and document group
     *
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function multipleUpload(
        ValidationService $validationService,
        FileService $fileService
    ): JsonResponse {
        $validationErrors = $validationService->check('documents_multiple_upload');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $user = Auth::user();
        $kindergarten = Kindergarten::find(request()->kindergarten_id);

        if (!($user->can(PERM::UPLOAD_ANY_DOCUMENTS['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::UPLOAD_OWN_DOCUMENTS['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $documentGroup = DocumentGroup::find(request()->document_group_id);

        foreach (request()->upload as $d) {
            $file = $fileService->save($d, FT::DOCUMENT_FILE_TYPE['slug']);

            if ($file) {
                $document = new Document();
                $document->kindergarten()->associate($kindergarten);
                $document->documentGroup()->associate($documentGroup);
                $document->file_id = $file->id;

                $document->save();
            }
        }

        return $this->buildRes(200);
    }

    /**
     * Get document for specific kindergarten by document_group_id
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function get(ValidationService $validationService)
    {
        $validationErrors = $validationService->check('get_documents');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $documents = Document::where('kindergarten_id', request()->kindergarten_id);

        if (request()->filter) {
            $documentGroup = DocumentGroup::where('title', request()->filter)->first();
            $documents->where('document_group_id', '=', $documentGroup->id);
        }

        $documents = $documents->get();

        return $this->buildRes(200, $documents);
    }

    /**
     * Delete document
     *
     * @param string $documentId
     * @param FileService $fileService
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(string $documentId, FileService $fileService): JsonResponse
    {
        if (!is_numeric($documentId) || !$document = Document::find($documentId))
            return $this->buildRes(404);

        $user = Auth::user();

        if (!($user->can(PERM::DELETE_ANY_DOCUMENTS['slug']) ||
            ($user->id === $document->kindergarten->user_id && $user->can(PERM::DELETE_OWN_DOCUMENTS['slug'])))
        ) {
            return $this->buildRes(403);
        }

        if ($file = $document->file) {
            $fileService->delete($file);
        }

        $document->delete();

        return $this->buildRes(200);
    }
}
