<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Eloquent;
use Storage;

/**
 * App\Models\Document
 *
 * @property int $id
 * @property int $kindergarten_id
 * @property string|null $title
 * @property string|null $description
 * @property int $document_group_id
 * @property string $external_link
 * @property int $file_id
 * @property-read DocumentGroup $documentGroup
 * @property-read File|null $file
 * @property-read Kindergarten $kindergarten
 * @property string src
 * @mixin Eloquent
 */
class Document extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'description', 'external_link',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'document_group_id', 'documentGroup',
        'kindergarten_id', 'kindergarten',
        'file_id', 'file',
        'created_at', 'updated_at'
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
     * Associated documentGroup
     *
     * @return BelongsTo
     */
    public function documentGroup(): BelongsTo
    {
        return $this->belongsTo(DocumentGroup::class);
    }

    /**
     * Document file src attribute
     *
     * @return string|null
     */
    public function getSrcAttribute(): ?string
    {
        if ($this->file && Storage::exists($this->file->path))
            return asset('storage/' . $this->file->path);

        return null;
    }
}
