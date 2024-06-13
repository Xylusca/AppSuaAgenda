<?php

namespace App\Filament\Resources\SchedulingsResource\Pages;

use App\Filament\Resources\SchedulingsResource;
use App\Models\Scheduling;
use App\Models\ServiceScheduling;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSchedulings extends EditRecord
{
    protected static string $resource = SchedulingsResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $services_schedulings = Scheduling::find($data['id']);

        $services_schedulings = $services_schedulings->services_schedulings->toArray();

        $services_ids = array_map(function ($service) {
            return $service['service_id']; 
        }, $services_schedulings);

        $data['end_time'] = Carbon::createFromFormat('Y-m-d H:i:s', $data['end_time'])
            ->format('d/m/Y H:i');

        $data['start_time'] = Carbon::createFromFormat('Y-m-d H:i:s', $data['start_time'])
            ->format('d/m/Y H:i');

        $data['services_schedulings'] = $services_ids;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $existingServiceSchedulings = $record->services_schedulings->pluck('service_id')->toArray();

        $newServiceSchedulings = isset($data['services_schedulings']) ? (array)$data['services_schedulings'] : [];

        $serviceIdsToRemove = array_diff($existingServiceSchedulings, $newServiceSchedulings);

        $serviceIdsToAdd = array_diff($newServiceSchedulings, $existingServiceSchedulings);

        if (!empty($serviceIdsToRemove)) {
            $record->services_schedulings()->whereIn('service_id', $serviceIdsToRemove)->delete();
        }

        if (!empty($serviceIdsToAdd)) {
            foreach ($serviceIdsToAdd as $serviceId) {
                $record->services_schedulings()->create(['service_id' => $serviceId]);
            }
        }

        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
