<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityAttendees extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
    ];

    public function activity()
    {
        return $this->belongsTo(Activities::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
