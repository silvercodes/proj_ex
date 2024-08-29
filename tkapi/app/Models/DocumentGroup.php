<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Eloquent;

/**
 * App\Models\DocumentGroup
 *
 * @property int $id
 * @property string $title
 * @property string $title_ru
 * @property string $title_ua
 * @property string|null $description
 * @property-read Collection|Document[] $documents
 * @mixin Eloquent
 */
class DocumentGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'title_ru', 'title_ua', 'description',
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
     * Associated documents
     *
     * @return HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
