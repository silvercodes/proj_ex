<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Eloquent;
use App\Casts\JsonCast;

/**
 * Class Tt
 * @property int $id
 * @property string[]|null $subjects
 * @property int $tt_group_id
 * @property int $tt_day_id
 * @property int $tt_part_id
 * @mixin Eloquent
 * @package App\Models
 */
class Tt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'subjects', 'tt_group_id', 'tt_day_id', 'tt_part_id'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'subjects' => JsonCast::class,
    ];

    /**
     * Associated ttGroup
     *
     * @return BelongsTo
     */
    public function ttGroup(): BelongsTo
    {
        return $this->belongsTo(TtGroup::class);
    }

    /**
     * Associated ttDay
     *
     * @return BelongsTo
     */
    public function ttDay(): BelongsTo
    {
        return $this->belongsTo(TtDay::class);
    }

    /**
     * Associated ttPart
     *
     * @return BelongsTo
     */
    public function ttPart(): BelongsTo
    {
        return $this->belongsTo(TtPart::class);
    }

}
