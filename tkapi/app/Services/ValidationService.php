<?php

namespace App\Services;

use App\Support\Rules\ValidationRules;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;

/**
 * Class ValidationService
 * @package App\Services
 */
class ValidationService
{
    /**
     * Validate based on relevant rules
     *
     * @param string $rule
     * @param string|null $excludedId
     * @param array|null $data
     * @return MessageBag|null
     */
    public function check(string $rule, ?string $excludedId = null, array $data = null): ?MessageBag
    {
        $data = $data ?? request()->all();

        $rules = ValidationRules::get($rule);

        if ($excludedId) {
            $this->customizeRulesForUpdate($rules, $excludedId);
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails())
            return $validator->errors();

        return null;
    }

    /**
     * Customizing rules for excluding fild by id from validation
     *
     * @param array $rules
     * @param string $id
     * @return void
     */
    private function customizeRulesForUpdate(array &$rules, string $id): void
    {
        array_walk($rules, function(&$rule, $key) use($id) {
            if (strpos($rule, ',' . $key . ','))
                $rule .= $id;
        });
    }

}
