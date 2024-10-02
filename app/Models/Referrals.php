<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referrals extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'referrer_id',
        'referred_id',
    ];

    // The user who referred another user
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    // The user who was referred
    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_id');
    }
}
