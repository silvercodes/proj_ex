<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Casts\StubForNullString;
use Storage;
use Eloquent;

/**
 * App\Models\Employee
 *
 * @property int $id
 * @property string|null $full_name
 * @property string|null $position
 * @property string|null $education
 * @property string|null $teaching_experience
 * @property string|null $management_experience
 * @property string|null $awards
 * @property bool is_administration
 * @property int $file_id
 * @property-read File|null $file
 * @property int $kindergarten_id
 * @property-read Kindergarten|null $kindergarten
 * @property int $kindergarten_group_id
 * @property string employee_photo
 * @mixin Eloquent
 */
class Employee extends Model
{
    /**
     *  Default photo path if an photo file not found
     */
    const DEFAULT_EMPLOYEE_PHOTO_PATH = 'img/default_employee_photo.jpg';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'kindergarten_group_id',
        'full_name',
        'position',
        'education',
        'teaching_experience',
        'management_experience',
        'awards',
        'is_administration',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'file_id', 'file',
        'kindergarten_id', 'kindergarten',
        'kindergartenGroup',
        'created_at', 'updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'employee_photo',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'education' => StubForNullString::class,
        'teaching_experience' => StubForNullString::class,
        'management_experience' => StubForNullString::class,
        'is_administration' => 'boolean',
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
     * Associated file
     *
     * @return HasOne
     */
    public function file(): HasOne
    {
        return $this->hasOne(File::class, 'id', 'file_id');
    }

    /**
     * Associated kindergarten group
     *
     * @return BelongsTo
     */
    public function kindergartenGroup(): BelongsTo
    {
        return $this->belongsTo(KindergartenGroup::class);
    }

    /**
     * Employee photo file url attribute
     *
     * @return string
     */
    public function getEmployeePhotoAttribute(): string
    {
        if ($this->file && Storage::exists($this->file->path))
            return asset('storage/' . $this->file->path);

        return asset(self::DEFAULT_EMPLOYEE_PHOTO_PATH);
    }

}
