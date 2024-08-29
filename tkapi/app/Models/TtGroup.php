<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Eloquent;

/**
 * Class TtGroup
 * @property int $id
 * @property string $title
 * @property int $tt_group_type_id
 * @property int $kindergarten_id
 * @property-read Kindergarten $kindergarten
 * @mixin Eloquent
 * @package App\Models
 */
class TtGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'ttGroupType',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'tt_group_type_id',
        'kindergarten_id',
        'kindergarten',
        'created_at',
        'updated_at'
    ];

    /**
     * Associated ttGroupType
     *
     * @return BelongsTo
     */
    public function ttGroupType(): BelongsTo
    {
        return $this->belongsTo(TtGroupType::class);
    }

    /**
     * Associated kindergarten
     *
     * @return BelongsTo
     */
    public function kindergarten(): BelongsTo
    {
        return $this->belongsTo(Kindergarten::class);
    }

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
