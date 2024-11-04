<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'phone',
        'verification_code',
        'phone_verified_at',
        'password',
        'referred_by',
        'points',
        'level',
        'is_active',
        'id_status',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referrals::class, 'referrer_id', 'id');
    }

    public function downlineUsers()
    {
        return $this->hasManyThrough(
            User::class,
            Referrals::class,
            'referrer_id', // Foreign key on referrals table
            'id', // Foreign key on users table
            'id', // Local key on users table
            'referred_id' // Local key on referrals table
        );
    }

    // Recursive relationship for fetching all downlines
    public function allDownlineUsers()
    {
        return $this->referrals()->with('referredUser.allDownlineUsers');
    }

// In Referral.php
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    // The transactions related to this user
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }
}
