<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public function team()
    {
        return $this->belongsToMany(Team::class, 'team_members');
    }

    public function assignedLetters()
    {
        return $this->hasMany(Letter::class, 'send_to', 'id');
    }

    public function completedDeliveries()
    {
        return $this->hasMany(Delivery::class, 'delivered_to_user_id', 'id');
    }

    public function deliveredLetters()
    {
        return $this->assignedLetters()->where('status', 'delivered');
    }

    
    const ROLE_SUPER_ADMIN = 'super admin';
    const ROLE_RECEPTIONIST = 'Receptionist';
    const ROLE_PEON = 'Peon';
    const ROLE_MEMBER = 'Member';

  
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isReceptionist()
    {
        return $this->role === self::ROLE_RECEPTIONIST;
    }

    public function isPeon()
    {
        return $this->role === self::ROLE_PEON;
    }

    public function isMember()
    {
        return $this->role === self::ROLE_MEMBER;
    }
}
