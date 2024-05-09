<?php

namespace App\Filament\Resources\SchedulingsResource\Pages;

use App\Filament\Resources\SchedulingsResource;
use App\Models\Scheduling;
use App\Models\ServiceScheduling;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSchedulings extends CreateRecord
{
    protected static string $resource = SchedulingsResource::class;
    
    protected function handleRecordCreation(array $data): Model
    {
        $scheduling = Scheduling::create($data);

        foreach($data['services_schedulings'] as $ServiceSchedulingUni){
            $serviceScheduling = ServiceScheduling::create([
                'service_id' => $ServiceSchedulingUni, 
                'scheduling_id' => $scheduling->id
            ]);
        }

        return $scheduling;
    }
}
