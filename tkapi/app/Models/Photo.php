<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Eloquent;
use Storage;

/**
 * App\Models\Photo
 *
 * @property int $id
 * @property int $album_id
 * @property string|null $title
 * @property string|null $description
 * @property int $file_id
 * @property-read Album $album
 * @property-read File|null $file
 * @property string src
 * @mixin Eloquent
 */
class Photo extends Model
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
        'album_id', 'file_id', 'file', 'created_at', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'src',
    ];

    /**
     * Associated album
     *
     * @return BelongsTo
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * Associated file
     *
     * @return HasOne
     */
    public function file(): HasOne
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    /**
     * Photo file src attribute
     *
     * @return string
     */
    public function getSrcAttribute(): string
    {
        if ($this->file && Storage::exists($this->file->path))
            return asset('storage/' . $this->file->path);

        return asset(self::DEFAULT_PHOTO_PATH);
    }

}
