<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Eloquent;
use Storage;

/**
 * App\Models\Album
 *
 * @property int $id
 * @property int $kindergarten_id
 * @property string $title
 * @property string|null $description
 * @property-read Collection|Photo[] $photos
 * @property-read Kindergarten $kindergarten
 * @mixin Eloquent
 */
class Album extends Model
{
    /**
     *  Default photo path if an photo file not found
     */
    const DEFAULT_PHOTO_PATH = 'img/default_photo.jpg';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'kindergarten_id', 'kindergarten', 'created_at', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'cover',
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
     * Associated photos
     *
     * @return HasMany
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Album cover
     *
     * @return string
     */
    public function getCoverAttribute(): string
    {
        $photo = $this->photos()->first();

        if ($photo && $photo->file && Storage::exists($photo->file->path))
            return asset('storage/' . $photo->file->path);

        return asset(self::DEFAULT_PHOTO_PATH);

    }
}
