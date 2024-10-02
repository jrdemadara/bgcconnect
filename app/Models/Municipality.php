<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipality extends Model
{
    use HasFactory;
    protected $connection = 'mariadb_address';
    protected $table = 'address_citymun';
    protected $fillable = [
        'citymunCode',
        'citymunDescription',
    ];
}
