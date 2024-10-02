<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RaffleEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tier_id',
        'draw_id',
    ];

}
