<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\Service;
use App\Models\RequestService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
    public function requestServices()
    {
        return $this->hasMany(RequestService::class);
    }
    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }
    public function isUser()
    {
        return $this->role->name === 'user';
    }
    public function hasRole($role)
    {
        return $this->role->name === $role;
    }
    public function hasPermission($permission)
    {
    $userPermissions = $this->role->permissions->pluck('name')->toArray();

    return in_array($permission, $userPermissions);
    }
    
}
