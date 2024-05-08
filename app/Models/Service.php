<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'duration',
        'desc',
        'image_path'
    ];

    protected $dates = ['deleted_at'];

    public function getFormattedPrice(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    public function hasImage(): bool
    {
        return !empty($this->image_path);
    }
}
