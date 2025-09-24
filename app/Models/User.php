<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
     * @var array<int, string>
     */

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'loyalty_points',
        'user_type',
        'is_active',
        'provider',
        'provider_id',
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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */



    public function isAdmin()
    {
        return $this->user_type === 'Admin';
    }

    // Check if user is customer
    public function isCustomer()
    {
        return $this->user_type === 'Customer';
    }
    // Check if user is staff
    public function isStaff()
    {
        return $this->user_type === 'Staff';
    }

    // Check if user is active
    public function isActive()
    {
        return $this->is_active == 1;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'user_id');
    }
}
