<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use Exception;
use App\Support\Enums\PermissionsEnum as PERM;
use App\Support\Enums\FileTypesEnum as FT;
use App\Services\FileService;
use App\Services\ValidationService;
use App\Models\Employee;
use App\Models\Kindergarten;
use App\Models\KindergartenGroup;


/**
 * Class EmployeeController
 * @package App\Http\Controllers\Api
 */
class EmployeeController extends ApiController
{
    /**
     * Add employee
     *
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function add(ValidationService $validationService, FileService $fileService): JsonResponse
    {
        $validationErrors = $validationService->check('add_employee');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $kindergarten = Kindergarten::find(request()->kindergarten_id);
        $kindergartenGroup = KindergartenGroup::find(request()->kindergarten_group_id);

        $user = Auth::user();

        if ($user->can(PERM::ADD_ANY_EMPLOYEE['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::ADD_OWN_EMPLOYEE['slug']))
        ) {
            $employee = new Employee();
            $employee->full_name = request()->full_name;
            $employee->position = request()->position;
            $employee->education = request()->education;
            $employee->teaching_experience = request()->teaching_experience;
            $employee->management_experience = request()->management_experience;
            $employee->awards = request()->awards;
            $employee->is_administration = request()->is_administration;
            $employee->kindergarten()->associate($kindergarten);
            $employee->kindergartenGroup()->associate($kindergartenGroup);

            if (request()->employee_photo) {
                $file = $fileService->save(request()->employee_photo, FT::EMPLOYEE_PHOTO_FILE_TYPE['slug']);

                if ($file)
                    $employee->file_id = $file->id;
            }

            $employee->save();

        } else {
            return $this->buildRes(403);
        }

        return $this->buildRes(200, $employee);
    }

    /**
     * Get all employees by kindergarten id
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function getByKindergartenId(ValidationService $validationService): JsonResponse
    {
        $validationErrors = $validationService->check('get_employees_by_kindergarten_id');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $employees = Employee::where('kindergarten_id', request()->kindergarten_id);

        switch(request()->filter)
        {
            case 'administration':
                $employees->where('is_administration', true);
                break;
            case 'educator':
                $employees->where('is_administration', false);
                break;
        }

        $employees = $employees->get();

        return $this->buildRes(200, $employees);
    }

    /**
     * Delete employee
     *
     * @param string $employeeId
     * @param FileService $fileService
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(string $employeeId, FileService $fileService): JsonResponse
    {
        if (!is_numeric($employeeId) || !$album = Employee::find($employeeId))
            return $this->buildRes(404);

        $user = Auth::user();

        $employee = Employee::find($employeeId);

        if (!($user->can(PERM::DELETE_ANY_EMPLOYEE['slug']) ||
            ($user->id === $employee->kindergarten->user_id && $user->can(PERM::DELETE_OWN_EMPLOYEE['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $employeePhotoFile = $employee->file;
        if ($employeePhotoFile)
            $fileService->delete($employeePhotoFile);

        $employee->delete();

        return $this->buildRes(200);
    }

    /**
     * Patch employee
     *
     * @param string $employeeId
     * @param ValidationService $validationService
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function patch (
        string $employeeId,
        ValidationService $validationService,
        FileService $fileService
    ) :JsonResponse {
        if (!is_numeric($employeeId) || !$employee = Employee::find($employeeId))
            return $this->buildRes(404);

        $user = Auth::user();

        if (!($user->can(PERM::PATCH_ANY_EMPLOYEE['slug']) ||
            ($user->id === $employee->kindergarten->user_id && $user->can(PERM::PATCH_OWN_EMPLOYEE['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $validationErrors = $validationService->check('patch_employee');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $employee->update(request()->only([
            'full_name',
            'position',
            'education',
            'teaching_experience',
            'management_experience',
            'awards',
            'is_administration',
        ]));

        // KindergartenGroup
        if (request()->kindergarten_group_id) {
            $employee->kindergartenGroup()->associate(KindergartenGroup::find(request()->kindergarten_group_id));
            $employee->save();
        }

        // File
        if (request()->employee_photo) {
            $oldFile = $employee->file;

            $file = $fileService->save(request()->employee_photo, FT::EMPLOYEE_PHOTO_FILE_TYPE['slug']);

            if ($file) {
                $employee->file_id = $file->id;
                if($oldFile)
                    $fileService->delete($oldFile);
                $employee->save();
            }
        }

        return $this->buildRes(200, Employee::find($employeeId));
    }
}
