<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Activities extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'location',
        'date_start',
        'date_end',
        'points',
    ];

    public function activity_attendees()
    {
        return $this->hasMany(ActivityAttendees::class);
    }
}
