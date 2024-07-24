<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SchedulingsResource;
use App\Filament\Resources\SchedulingsResource\Pages\CreateSchedulings;
use App\Models\Scheduling;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class Schedules extends FullCalendarWidget
{
    protected int | string | array $columnSpan = 'full';

    protected $getSort = 2;

    public Model | string | null $model = Scheduling::class;

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'dayGridMonth,dayGridWeek',
                'center' => 'title',
                'right' => 'prev,next',
            ],
            'aspectRatio' => 1.35,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Scheduling::query()
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('end_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Scheduling $scheduling) => [
                    'title' => $scheduling->name . ' / ' . $scheduling->whats,
                    'start' => $scheduling->start_time,
                    'end' => $scheduling->end_time,
                    'color' => match ($scheduling->status) {
                        'Aguardando' => 'yellow',
                        'Concluído' => 'info',
                        'Cancelado' => 'red',
                        default => 'gray',
                    },
                    'url' => SchedulingsResource::getUrl(name: 'edit', parameters: ['record' => $scheduling]),
                    'shouldOpenUrlInNewTab' => false
                ]
            )
            ->all();
    }

    protected function headerActions(): array
    {
        return [];
    }
}
