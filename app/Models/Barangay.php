<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barangay extends Model
{
    use HasFactory;
    protected $connection = 'mariadb_address';
    protected $table = 'address_barangay';
    protected $fillable = [
        'brgyCode',
        'brgyDescription',
    ];
}
