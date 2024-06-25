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

        $monthlyAnalysisProfit = $this->monthlyAnalysisProfit();

        return [
            Stat::make('Análise Anual de Agendamentos', $annualScheduleAnalysis['currentYear'])
                ->description($annualScheduleAnalysis['differentiates'])
                ->descriptionIcon($annualScheduleAnalysis['descriptionIcon'])
                ->color($annualScheduleAnalysis['color']),
            Stat::make('Análise Mensal de Agendamentos', $monthScheduleAnalysis['currentMonth'])
                ->description($monthScheduleAnalysis['differentiates'])
                ->descriptionIcon($monthScheduleAnalysis['descriptionIcon'])
                ->color($monthScheduleAnalysis['color']),
            Stat::make('Lucro Mensal dos Agendamentos', $monthlyAnalysisProfit['profiCurrentMonth'])
                ->description($monthlyAnalysisProfit['differentiates'])
                ->descriptionIcon($monthlyAnalysisProfit['descriptionIcon'])
                ->color($monthlyAnalysisProfit['color']),
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

    protected function monthlyAnalysisProfit(): array
    {
        $currentDate = Carbon::now();

        $firstDayCurrentMonth = $currentDate->copy()->startOfMonth();
        $lastDayCurrentMonth = $currentDate->copy()->endOfMonth();

        $firstDayLastMonth = $currentDate->copy()->subMonth()->startOfMonth();
        $lastDayLastMonth = $currentDate->copy()->subMonth()->endOfMonth();

        $profitCurrentMonth = Scheduling::where('status', 'Concluído')
            ->whereBetween('start_time', [$firstDayCurrentMonth, $lastDayCurrentMonth])
            ->with('services_schedulings.service')
            ->get();

        $profitLastMonth = Scheduling::where('status', 'Concluído')
            ->whereBetween('start_time', [$firstDayLastMonth, $lastDayLastMonth])
            ->with('services_schedulings.service')
            ->get();

        $TotalProfitCurrentMonth = 0;
        $TotalProfitLastMonth = 0;

        foreach ($profitCurrentMonth as $scheduling) {
            foreach ($scheduling->services_schedulings as $serviceScheduling) {
                $service = $serviceScheduling->service;
                $TotalProfitCurrentMonth += $service->price;
            }
        }

        foreach ($profitLastMonth as $scheduling) {
            foreach ($scheduling->services_schedulings as $serviceScheduling) {
                $service = $serviceScheduling->service;
                $TotalProfitLastMonth += $service->price;
            }
        }

        if ($TotalProfitCurrentMonth >= $TotalProfitLastMonth) {
            $differentiates = $TotalProfitCurrentMonth - $TotalProfitLastMonth;

            return [
                'profiCurrentMonth' => 'R$ ' . number_format($TotalProfitCurrentMonth, 2, ',', '.'),
                'differentiates' => 'R$ ' . number_format($differentiates, 2, ',', '.') . ' Aumento',
                'color' => 'success',
                'descriptionIcon' => 'heroicon-m-arrow-trending-up'
            ];
        } else {
            $differentiates = $TotalProfitLastMonth - $TotalProfitCurrentMonth;

            return [
                'profiCurrentMonth' => 'R$ ' . number_format($TotalProfitCurrentMonth, 2, ',', '.'),
                'differentiates' => 'R$ ' . number_format($differentiates, 2, ',', '.') . ' Reduziu',
                'color' => 'danger',
                'descriptionIcon' => 'heroicon-m-arrow-trending-down'
            ];
        }
    }
}
