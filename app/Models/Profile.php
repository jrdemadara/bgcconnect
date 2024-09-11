<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'lastname',
        'firstname',
        'middlename',
        'extension',
        'precinct_number',
        'avatar',
        'id_type',
        'id_card_front',
        'id_card_back',
        'region',
        'province',
        'municipality_city',
        'barangay',
        'street',
        'gender',
        'birthdate',
        'civil_status',
        'blood_type',
        'religion',
        'tribe',
        'industry_sector',
        'occupation',
        'income_level',
        'affiliation',
        'facebook',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}