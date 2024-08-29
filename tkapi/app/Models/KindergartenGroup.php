<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class KindergartenGroup
 * @property int $id
 * @property string $title
 * @property int $kindergarten_id
 * @property-read Kindergarten $kindergarten
 * @property-read Collection|File[] $files
 * @property-read Collection|Employee[] $employees
 * @mixin Eloquent
 * @package App\Models
 */
class KindergartenGroup extends Model
{
    /**
     *  Default image path if a images files not found
     */
    const DEFAULT_IMAGE_PATH = 'img/default_photo.jpg';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'files', 'kindergarten_id', 'kindergarten', 'created_at', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'images',
        'educators',
    ];

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
     * Associated files
     *
     * @return BelongsToMany
     */
    public function files(): BelongsToMany
    {
        return $this->belongsToMany(File::class, 'kindergarten_groups_files');
    }

    /**
     * Associated employees
     *
     * @return HasMany
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'kindergarten_group_id', 'id');
    }

    /**
     * Associated images attribute
     *
     * @return array
     */
    public function getImagesAttribute(): array
    {
        $images = [];
        foreach ($this->files as $file) {
            $images[] = [
                'src' => $file->url,
                'file_id' => $file->id,
            ];
        }

        if (!$images)
            $images[] = [
                'src' => asset(self::DEFAULT_IMAGE_PATH),
                'file_id' => null,
            ];

        return $images;
    }

    /**
     * Get associated educators attribute
     *
     * @return Collection
     */
    public function getEducatorsAttribute()
    {
        return $this->employees()->where('is_administration', false)->get();
    }

}
