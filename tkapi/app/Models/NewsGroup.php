<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquent;

/**
 * Class NewsGroup
 * @property int $id
 * @property string $title
 * @property string $title_ru
 * @property string $title_ua
 * @mixin Eloquent

 * @package App\Models
 */
class NewsGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'title_ru', 'title_ua'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
