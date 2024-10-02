<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RaffleDraw extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'draws';
    protected $fillable = [
        'draw_date',
    ];

}
