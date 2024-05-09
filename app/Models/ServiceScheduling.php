<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceScheduling extends Model
{
    use HasFactory;

    protected $table = 'services_schedulings';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = ['service_id', 'scheduling_id'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function scheduling()
    {
        return $this->belongsTo(Scheduling::class, 'scheduling_id');
    }
}
