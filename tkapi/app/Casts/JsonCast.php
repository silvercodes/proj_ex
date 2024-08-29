<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Class StubForNullString
 * @package App\Casts
 */
class JsonCast implements CastsAttributes
{

    public function get($model, string $key, $value, array $attributes)
    {
        return json_decode($value);
    }


    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value);
    }
}
