<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'phone_number',
        'role',
        'is_blocked',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $appends = ['is_owner'];
    protected $table = 'users';
    protected $primaryKey = 'id';

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getIsOwnerAttribute()
    {
        return $this->role === 'owner';
    }

    public function owner()
    {
        return $this->hasOne(Owner::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'customer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }
}