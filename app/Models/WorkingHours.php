<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingHours extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'weekday',
        'open_time',
        'close_time',
    ];

    protected $dates = ['deleted_at'];
}
