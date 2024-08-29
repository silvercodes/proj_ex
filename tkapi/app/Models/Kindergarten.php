<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Eloquent;
use App\User;

/**
 * App\Models\Kindergarten
 *
 * @property int $id
 * @property string $title
 * @property string $address
 * @property float $lat
 * @property float $lng
 * @property int $user_id
 * @property-read Collection|Album[] $albums
 * @property-read Collection|Document[] $documents
 * @property-read User $user
 * @property-read Collection|KindergartenGroup[] $kindergartenGroups
 * @mixin Eloquent
 */
class Kindergarten extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'address', 'lat', 'lng',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id', 'created_at', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'class',
    ];

    /**
     * Associated user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Associated albums
     *
     * @return HasMany
     */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Associated documents
     *
     * @return HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

     /**
     * Associated employees
     *
     * @return HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Associated kindergarden groups
     *
     * @return HasMany
     */
    public function kindergartenGroups(): HasMany
    {
        return $this->hasMany(KindergartenGroup::class);
    }

    /**
     * Associated ttGroups
     *
     * @return HasMany
     */
    public function ttGroups()
    {
        return $this->hasMany(TtGroup::class);
    }


    /**
     * A frontend crutch because the frontend doesn't want to do anything
     *
     * @return string
     */
    public function getClassAttribute()
    {
        $colorArr = [
            'yellow',
            'green',
            'red',
            'purple',
            'blue',
        ];

        return $colorArr[array_rand($colorArr)];
    }

}
