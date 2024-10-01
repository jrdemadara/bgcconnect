<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tier',
        'points',
    ];

    public function prizes()
    {
        return $this->hasMany(RafflePrize::class, 'tier_id', 'id');
    }

}
