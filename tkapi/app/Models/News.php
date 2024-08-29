<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Eloquent;
use Storage;

/**
 * Class News
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $kindergarten_id
 * @property-read Kindergarten $kindergarten
 * @property int $news_group_id
 * @property-read NewsGroup $newsGroup
 * @property int $file_id
 * @property-read File|null $file
 * @property int $album_id
 * @property-read Album|null $album
 * @mixin Eloquent

 * @package App\Models
 */
class News extends Model
{
    /**
     *  Default image path if an image file not found
     */
    const DEFAULT_PHOTO_PATH = 'img/default_photo.jpg';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'description',
        'newsGroup',
        'album',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'file_id', 'file',
        'kindergarten_id', 'kindergarten',
        'news_group_id',
        'album_id',
        'created_at', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'news_image',
    ];

    /**
     * Image url attribute
     *
     * @return string
     */
    public function getNewsImageAttribute(): string
    {
        if ($this->file && Storage::exists($this->file->path))
            return asset('storage/' . $this->file->path);

        return asset(self::DEFAULT_PHOTO_PATH);
    }

    /**
     * Associated news group
     *
     * @return BelongsTo
     */
    public function newsGroup(): BelongsTo
    {
        return $this->belongsTo(NewsGroup::class);

    }

    /**
     * Associated file
     *
     * @return HasOne
     */
    public function file()
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    /**
     * Associated kindergarten
     *
     * @return BelongsTo
     */
    public function kindergarten()
    {
        return $this->belongsTo(Kindergarten::class);
    }

    /**
     * Associated album
     *
     * @return HasOne
     */
    public function album(): HasOne
    {
        return $this->hasOne(Album::class, 'id', 'album_id');
    }

}
