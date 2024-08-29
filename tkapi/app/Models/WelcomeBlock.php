<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Eloquent;

/**
 * Class WelcomeBlock
 * @package App\Models
 *
 * @property int id
 * @property int $kindergarten_id
// * @property-read Kindergarten|null $kindergarten
 * @property string|null title
 * @property string|null text
 * @property int $file_id
 * @property-read File|null $file
 * @property string $image
 * @mixin Eloquent
 */
class WelcomeBlock extends Model
{
    /**
     *  Default photo path if an photo file not found
     */
    const DEFAULT_WB_IMAGE_PATH = 'img/default_wb_image.png';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'text',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'file_id', 'file',
        'kindergarten_id', 'kindergarten',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image',
    ];

    /**
     * Welcome block image file url attribute
     *
     * @return string
     */
    public function getImageAttribute(): string
    {
        if ($this->file && Storage::exists($this->file->path))
            return asset('storage/' . $this->file->path);

        return asset(self::DEFAULT_WB_IMAGE_PATH);
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
}
