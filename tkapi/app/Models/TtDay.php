<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Eloquent;

/**
 * Class TtDay
 * @property int $id
 * @property string $title
 * @property string $title_ru
 * @property string $title_ua
 * @mixin Eloquent
 * @package App\Models
 */
class TtDay extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'title_ru', 'title_ua'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Associated tts
     *
     * @return HasMany
     */
    public function tts(): HasMany
    {
        return $this->hasMany(Tt::class);
    }
}
