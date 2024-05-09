<?php

namespace App\Filament\Resources\SchedulingsResource\Pages;

use App\Filament\Resources\SchedulingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchedulings extends ListRecords
{
    protected static string $resource = SchedulingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
