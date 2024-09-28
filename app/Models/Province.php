<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $connection = 'mariadb_address';
    protected $table = 'address_province';
    protected $fillable = [
        'provCode',
        'provDescription',
    ];
}
