<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Eloquent;
use App\Traits\HasPermissionsTrait;
use App\Models\Permission;
use App\Models\Role;
use App\Models\File;
use App\Models\Kindergarten;


/**
 * Class User
 *
 * @package App
 * @property int $id
 * @property string $name
 * @property string $email
 * @property-read Collection|Permission[] $permissions
 * @property-read Collection|Role[] $roles
 * @property-read Kindergarten $kindergarten
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasPermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'roles', 'permissions', 'all_permissions',
        'password', 'remember_token', 'email_verified_at', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'all_permissions',
    ];

    /**
     * Associated files
     *
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Associated kindergarten
     *
     * @return HasOne
     */
    public function kindergarten(): HasOne
    {
        return $this->hasOne(Kindergarten::class);
    }

    public function getAllPermissionsAttribute()
    {
        $allPermissions = [];

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                if (!in_array($permission->slug, $allPermissions))
                    $allPermissions[] = $permission->slug;
            }
        }

        foreach ($this->permissions as $permission) {
            if (!in_array($permission->slug, $allPermissions))
                $allPermissions[] = $permission->slug;
        }

        return $allPermissions;
    }
}
