<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Eloquent;
use Storage;
use App\User;


/**
 * class File
 *
 * @package App\Models
 * @property int $id
 * @property string $original_name
 * @property string $original_extension
 * @property string $path
 * @property-read string $url
 * @property int $user_id
 * @property-read Collection|User[] $users
 * @mixin Eloquent
 */
class File extends Model
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
        'id', 'original_name', 'original_extension', 'path', 'user_id'
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url',
    ];

    /**
     * Associated users
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * File url attribute
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        if (Storage::exists($this->path))
            return asset('storage/' . $this->path);

        return asset(self::DEFAULT_PHOTO_PATH);
    }
}
