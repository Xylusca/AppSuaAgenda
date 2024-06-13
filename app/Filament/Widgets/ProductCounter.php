<?php

namespace App\Filament\Widgets;

use App\Models\Scheduling;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ProductCounter extends BaseWidget
{
    protected function getStats(): array
    {
        $annualScheduleAnalysis = $this->annualScheduleCount();

        $monthScheduleAnalysis = $this->monthScheduleCount();

        // $currentDate = Carbon::now();

        // $firstDayCurrentMonth = $currentDate->copy()->startOfMonth();
        // $lastDayCurrentMonth = $currentDate->copy()->endOfMonth();

        // $currentMonthTotalPrice = Scheduling::where('status', 'Concluído')
        //     ->whereHas('services_schedulings', function ($query) use ($firstDayCurrentMonth, $lastDayCurrentMonth) {
        //         $query->whereBetween('start_time', [$firstDayCurrentMonth, $lastDayCurrentMonth]);
        //     })
        //     ->with(['services_schedulings' => function ($query) use ($firstDayCurrentMonth, $lastDayCurrentMonth) {
        //         $query->whereBetween('start_time', [$firstDayCurrentMonth, $lastDayCurrentMonth]);
        //     }])
        //     ->get()
        //     ->pluck('services_schedulings')
        //     ->flatten()
        //     ->sum('price');

        // dd($currentMonthTotalPrice);

        return [
            Stat::make('Análise Anual de Agendamentos', $annualScheduleAnalysis['currentYear'])
                ->description($annualScheduleAnalysis['differentiates'])
                ->descriptionIcon($annualScheduleAnalysis['descriptionIcon'])
                ->color($annualScheduleAnalysis['color']),
            Stat::make('Análise Mensal de Agendamentos', $monthScheduleAnalysis['currentMonth'])
                ->description($monthScheduleAnalysis['differentiates'])
                ->descriptionIcon($monthScheduleAnalysis['descriptionIcon'])
                ->color($monthScheduleAnalysis['color']),
            Stat::make('Lucro Mensal dos Agendamentos', 'R$ 1.800,00')
                ->description('R$254 Aumento')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }

    protected function annualScheduleCount(): array
    {
        $currentYear = Carbon::now()->year;
        $lastYear = Carbon::now()->subYear()->year;

        $currentYearCount = Scheduling::where('status', 'Concluído')
            ->whereHas('services_schedulings', function ($query) use ($currentYear) {
                $query->whereYear('start_time', $currentYear);
            })
            ->withCount(['services_schedulings as services_count' => function ($query) use ($currentYear) {
                $query->whereYear('start_time', $currentYear);
            }])
            ->get()
            ->sum('services_count');

        $lastYearCount = Scheduling::where('status', 'Concluído')
            ->whereHas('services_schedulings', function ($query) use ($lastYear) {
                $query->whereYear('start_time', $lastYear);
            })
            ->withCount(['services_schedulings as services_count' => function ($query) use ($lastYear) {
                $query->whereYear('start_time', $lastYear);
            }])
            ->get()
            ->sum('services_count');


        if ($currentYearCount >= $lastYearCount) {
            $differentiates = $currentYearCount - $lastYearCount;

            return [
                'currentYear' => $currentYearCount,
                'differentiates' => $differentiates . ' Aumento',
                'color' => 'success',
                'descriptionIcon' => 'heroicon-m-arrow-trending-up'
            ];
        } else {
            $differentiates = $lastYearCount - $currentYearCount;

            return [
                'currentYear' => $currentYearCount,
                'differentiates' => $differentiates . ' Reduziu',
                'color' => 'danger',
                'descriptionIcon' => 'heroicon-m-arrow-trending-down'
            ];
        }
    }

    protected function monthScheduleCount(): array
    {
        $currentDate = Carbon::now();

        $firstDayCurrentMonth = $currentDate->copy()->startOfMonth();
        $lastDayCurrentMonth = $currentDate->copy()->endOfMonth();

        $firstDayLastMonth = $currentDate->copy()->subMonth()->startOfMonth();
        $lastDayLastMonth = $currentDate->copy()->subMonth()->endOfMonth();

        $currentMonthCount = Scheduling::where('status', 'Concluído')
            ->whereHas('services_schedulings', function ($query) use ($firstDayCurrentMonth, $lastDayCurrentMonth) {
                $query->whereBetween('start_time', [$firstDayCurrentMonth, $lastDayCurrentMonth]);
            })
            ->withCount(['services_schedulings as services_count' => function ($query) use ($firstDayCurrentMonth, $lastDayCurrentMonth) {
                $query->whereBetween('start_time', [$firstDayCurrentMonth, $lastDayCurrentMonth]);
            }])
            ->get()
            ->sum('services_count');

        $lastMonthCount = Scheduling::where('status', 'Concluído')
            ->whereHas('services_schedulings', function ($query) use ($firstDayLastMonth, $lastDayLastMonth) {
                $query->whereBetween('start_time', [$firstDayLastMonth, $lastDayLastMonth]);
            })
            ->withCount(['services_schedulings as services_count' => function ($query) use ($firstDayLastMonth, $lastDayLastMonth) {
                $query->whereBetween('start_time', [$firstDayLastMonth, $lastDayLastMonth]);
            }])
            ->get()
            ->sum('services_count');

        if ($currentMonthCount >= $lastMonthCount) {
            $differentiates = $currentMonthCount - $lastMonthCount;

            return [
                'currentMonth' => $currentMonthCount,
                'differentiates' => $differentiates . ' Aumento',
                'color' => 'success',
                'descriptionIcon' => 'heroicon-m-arrow-trending-up'
            ];
        } else {
            $differentiates = $lastMonthCount - $currentMonthCount;

            return [
                'currentMonth' => $currentMonthCount,
                'differentiates' => $differentiates . ' Reduziu',
                'color' => 'danger',
                'descriptionIcon' => 'heroicon-m-arrow-trending-down'
            ];
        }
    }
}
