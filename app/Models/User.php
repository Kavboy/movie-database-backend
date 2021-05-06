<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'password',
    ];

    public function getRoleAttribute($value) {
        return Role::find($value) ? Role::find($value)['role'] : NULL;
    }

    /**
     * Relation between user and role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role() {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    /**
     * Relationship between user and show
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function video(){
        return $this->belongsToMany(Video::class, 'user_video');
    }

    /**
     * checks if the user has the role in the parameter
     *
     * @param $role string
     * @return bool
     */
    public function hasRole($role) {
        if ($this->role == $role){
            return true;
        }
        return false;
    }

    /**
     * checks if the user has any of the $roles in the parameter
     *
     * @param string $roles
     * @return bool
     */
    public function hasAnyRoles(string $roles) {
        $roles = explode(':', $roles);

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted() {
        static::deleting(function ($user) {
            $user->video()->detach();
        });
    }
}
