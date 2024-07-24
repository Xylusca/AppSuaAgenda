<?php

namespace App\Filament\Resources\SchedulingsResource\Widgets;

use App\Filament\Resources\SchedulingsResource;
use App\Models\Scheduling;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;


class SchedulingsOverview extends FullCalendarWidget
{
    protected int | string | array $columnSpan = 'full';

    public Model | string | null $model = Scheduling::class;

    // protected static string $view = 'filament.resources.schedulings-resource.widgets.schedulings-overview';

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

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
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
                        default => 'gray', // Cor padrão para outros status
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
