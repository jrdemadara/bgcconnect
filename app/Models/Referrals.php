<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referrals extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'reffered_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
