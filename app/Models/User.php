<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;  // Add this import

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;  // Ensure HasApiTokens is included

    protected $fillable = [
        'name', 'last_name', 'email', 'password', 'user_type', 'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Define the relationship with subscriptions
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
