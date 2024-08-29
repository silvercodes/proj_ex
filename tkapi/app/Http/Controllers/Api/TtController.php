<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Controllers\ApiController;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TtsImport;
use App\Services\TtsExcelService;
use App\Services\ValidationService;
use App\Support\Enums\PermissionsEnum as PERM;
use App\Models\Kindergarten;
use App\Models\Tt;
use App\Models\TtDay;
use App\Models\TtGroup;
use App\Models\TtGroupType;
use App\Models\TtPart;


/**
 * Class TtController
 * @package App\Http\Controllers\Api
 */
class TtController extends ApiController
{
    /**
     * Create a tt
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function create(ValidationService $validationService):JsonResponse
    {
        $validationErrors = $validationService->check('create_tt');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $user = Auth::user();
        $ttGroup = TtGroup::find(request()->tt_group_id);
        $kindergarten = $ttGroup->kindergarten;

        if (!($user->can(PERM::CREATE_ANY_TT['slug']) ||
            ($user->id === $kindergarten->user_id && $user->can(PERM::CREATE_OWN_TT['slug'])))
        ) {
            return $this->buildRes(403);
        }

        $tt = new Tt();
        $tt->ttGroup()->associate($ttGroup);
        $tt->ttDay()->associate(TtDay::find(request()->tt_day_id));
        $tt->ttPart()->associate(TtPart::find(request()->tt_part_id));
        $tt->subjects = request()->subjects;

        $tt->save();

        return $this->buildRes(200, $tt);
    }


    /**
     * Get all tts by kindergarten id
     *
     * @param ValidationService $validationService
     * @return JsonResponse
     */
    public function getByKindergartenId(ValidationService $validationService)
    {
        $validationErrors = $validationService->check('get_tts_by_kindergarten_id');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        $kindergarten = Kindergarten::find(request()->kindergarten_id);

        $groupsIds = $kindergarten->ttGroups()->select('id')->get()->pluck('id')->toArray();

        $tts =
            Tt::with(['ttGroup', 'ttDay', 'ttPart', 'ttGroup.ttGroupType'])
                ->whereIn('tt_group_id', $groupsIds)
                ->get();

        $tts = $tts->groupBy([
            function($item) {
                return $item->ttPart->title_ua;
            },
            function($item) {
                return $item->ttGroup->ttGroupType->title_ua;
            },
            function($item) {
                return $item->ttDay->title_ua;
            },
            function($item) {
                return $item->ttGroup->title;
            },

        ]);

        $ttsArr = [];
        $index = 0;
        $sArr = [];
        foreach($tts as $key=>$part) {
            $ttsArr[] = [
                'part' => $key,
                'group_types' => []
            ];

            foreach($part as $typeKey=>$typeVal) {

                $sArrAll = [];
                $groupNames = [];
                foreach($typeVal as $dayKey=>$dayVal) {

                    $sArr['День неділі'] = $dayKey;

                    if (!$groupNames)
                        $groupNames = $dayVal->keys();

                    foreach ($dayVal as $key=>$item) {

                        $sArr[$key] = $item[0]['subjects'];
                    }

                    $sArrAll[] = $sArr;

                    $sArr = [];
                }

                array_push($ttsArr[$index]['group_types'], [
                    'group_type' => $typeKey,
                    'headers' => array_merge(['День неділі'], $groupNames->toArray()),
                    'rows' => $sArrAll,
                ]);

            }

            $index++;
        }

        $res = [
            'groups' => $kindergarten->ttGroups,
            'parts' => TtPart::get(),
            'days' => TtDay::get(),
            'types' => TtGroupType::get(),
            'tts' => collect($ttsArr),
        ];

        return $this->buildRes(200, $res);

    }

    /**
     * Upload Excel file for tts
     *
     * @param ValidationService $validationService
     * @param TtsExcelService $excelService
     * @return JsonResponse
     */
    public function upload(ValidationService $validationService, TtsExcelService $excelService)
    {
        $validationErrors = $validationService->check('upload_tts');
        if ($validationErrors)
            return $this->buildRes(400, [], $validationErrors);

        try {
            $path = request()->excel_file->store('temp');
            $dataCollection = Excel::toCollection(new TtsImport, $path);

            $kindergarten = Kindergarten::find(request()->kindergarten_id);
            $oldGroupsIds = $kindergarten->ttGroups->pluck('id');
            $oldTtsIds = Tt::whereIn('tt_group_id', $oldGroupsIds)->pluck('id');

            $excelService->parse($dataCollection[0], request()->kindergarten_id);

            TtGroup::whereIn('id', $oldGroupsIds)->delete();
            Tt::whereIn('id', $oldTtsIds)->delete();
            Storage::delete($path);

        } catch (Exception $e) {
            return $this->buildRes(400, [], [$e->getMessage()]);
        }

        return $this->buildRes(200);
    }

    /**
     * Dowload a tts template
     *
     * @return JsonResponse|StreamedResponse
     */
    public function downloadTemplate()
    {
        if (Storage::exists('docs/tts_template.xlsx'))
            return Storage::download('docs/tts_template.xlsx', 'расписание.xlsx');

        return $this->buildRes(404);
    }
}
