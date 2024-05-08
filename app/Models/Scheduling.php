<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scheduling extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'whats',
        'start_time',
        'end_time',
        'status',
        'service_id'
    ];

    protected $dates = ['deleted_at', 'start_time', 'end_time'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function getServiceName(): ?string
    {
        return $this->service->name ?? null;
    }
}
