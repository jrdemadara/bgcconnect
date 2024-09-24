<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;
    protected $connection = 'mysql_address';
    protected $table = 'address_citymun';
    protected $fillable = [
        'citymunCode',
        'citymunDescription',
    ];
}
