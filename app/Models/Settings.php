<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'multilevel_size',
        'direct_points',
        'downline_points',
        'activity_points',
        'channel_size',
        'last_channel',
    ];
}
