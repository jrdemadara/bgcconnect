<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RaffleTier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tier',
        'points',
    ];

    public function prizes()
    {
        return $this->hasMany(RafflePrize::class, 'tier_id', 'id');
    }

}
