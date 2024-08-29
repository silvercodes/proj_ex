<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Eloquent;
use App\User;


/**
 * Class Permission
 *
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property-read Collection|Role[] $roles
 * @property-read Collection|User[] $users
 * @mixin Eloquent
 */
class Permission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title', 'slug'
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
     * Associated roles
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }

    /**
     * Associated users
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_permissions');
    }
}
