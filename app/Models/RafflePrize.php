<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RafflePrize extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'raffle_prize';
    protected $fillable = [
        'name',
        'quantity',
        'tier_id',
    ];

    public function Tier()
    {
        return $this->belongsTo(RaffleTier::class);
    }

}
