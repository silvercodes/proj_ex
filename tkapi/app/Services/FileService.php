<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\CannotWriteFileException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Support\Enums\FileTypesEnum as FT;
use Storage;
use Auth;
use Exception;
use App\Models\File;

/**
 * Class FileService
 * @package App\Services
 */
class FileService
{
    /**
     * Save photo file
     *
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    private function savePhoto(UploadedFile $uploadedFile): ?string
    {
        return $uploadedFile->store(FT::PHOTO_FILE_TYPE['dir']);
    }

    /**
     * Save document file
     *
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    private function saveDocument(UploadedFile $uploadedFile): ?string
    {
        return $uploadedFile->store(FT::DOCUMENT_FILE_TYPE['dir']);
    }

    /**
     * Save employee photo
     *
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    private function saveEmployeePhoto(UploadedFile $uploadedFile): ?string
    {
        return $uploadedFile->store(FT::EMPLOYEE_PHOTO_FILE_TYPE['dir']);
    }

    /**
     * Save kindergarten group image
     *
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    private function saveKindergartenGroupImage(UploadedFile $uploadedFile): ?string
    {
        return $uploadedFile->store(FT::KINDERGARTEN_GROUP_IMAGE_FILE_TYPE['dir']);
    }

    /**
     * Save news image
     *
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    private function saveNewsImage(UploadedFile $uploadedFile): ?string
    {
        return $uploadedFile->store(FT::NEWS_IMAGE_FILE_TYPE['dir']);
    }

    /**
     * Save welcome block image
     *
     * @param UploadedFile $uploadedFile
     * @return string|null
     */
    private function saveWelcomeBlockImage(UploadedFile $uploadedFile): ?string
    {
        return $uploadedFile->store(FT::WELCOME_BLOCK_FILE_TYPE['dir']);
    }

    /**
     * Save file to storage/uploads
     *
     * @param UploadedFile $uploadedFile
     * @param string $fileTypeSlug
     * @return File|null
     */
    public function save(UploadedFile $uploadedFile, string $fileTypeSlug): ?File
    {
        $fileTypes = FT::getConstants();

        $fileType = [];
        foreach ($fileTypes as $ft) {
            if ($ft['slug'] === $fileTypeSlug)
                $fileType = $ft;
        }

        try {
            $path = null;

            switch ($fileType['slug']) {
                case FT::PHOTO_FILE_TYPE['slug']:
                    $path = $this->savePhoto($uploadedFile);
                    break;
                case FT::DOCUMENT_FILE_TYPE['slug']:
                    $path = $this->saveDocument($uploadedFile);
                    break;
                case FT::EMPLOYEE_PHOTO_FILE_TYPE['slug']:
                    $path = $this->saveEmployeePhoto($uploadedFile);
                    break;
                case FT::KINDERGARTEN_GROUP_IMAGE_FILE_TYPE['slug']:
                    $path = $this->saveKindergartenGroupImage($uploadedFile);
                    break;
                case FT::NEWS_IMAGE_FILE_TYPE['slug']:
                    $path = $this->saveNewsImage($uploadedFile);
                    break;
                case FT::WELCOME_BLOCK_FILE_TYPE['slug']:
                    $path = $this->saveWelcomeBlockImage($uploadedFile);
                    break;
            }

            if (!$path)
                throw new CannotWriteFileException();

            $file = new File();
            $file->original_name = $uploadedFile->getClientOriginalName();
            $file->original_extension = $uploadedFile->getClientOriginalExtension();
            $file->path = $path;
            $file->user()->associate(Auth::user());

            $file->save();

            return $file;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Delete file from storage/uploads
     *
     * @param File $file
     * @return bool
     */
    public function delete(File $file): bool
    {
        try {
            $path = $file->path;

            if (Storage::exists($path))
                Storage::delete($path);

            $file->delete();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get file URL
     *
     * @param File $file
     * @return string
     */
    public function getURL(File $file): string
    {
        if ($file && Storage::exists($file->path))
            return asset('storage/' . $file->path);

        return '';
    }

    /**
     * Get file stream
     *
     * @param File $file
     * @return StreamedResponse|null
     */
    public function getStream(File $file): ?StreamedResponse
    {
        if ($file && Storage::exists($file->path))
            return Storage::download($file->path, $file->original_name);

        return null;
    }
}
