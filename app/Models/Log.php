<?php
namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'logs';

    protected $fillable = ['type', 'content', 'ip_address'];
    protected $casts    = [
        'content' => 'array',
    ];
}
