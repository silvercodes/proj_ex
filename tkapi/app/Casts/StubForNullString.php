<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StubForNullString
 * @package App\Casts
 */
class StubForNullString implements CastsAttributes
{
    /**
     * Stub for null string
     */
    const STRING_STUB = '-';

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed|string
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (!$value)
            return self::STRING_STUB;
        return $value;
    }

    /**
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return array|mixed|string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
