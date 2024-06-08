<?php

namespace App\Http\Controllers;

use App\Models\Scheduling;
use App\Models\Service;
use App\Models\ServiceScheduling;
use App\Models\WorkingHours;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchedulingController extends Controller
{
    public function availableDays(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id_services' => 'required|string',
        ]);

        $servicesSelected = $this->parseServiceIds($validatedData['id_services']);
        $totalServiceTime = $this->calculateTotalServiceTime($servicesSelected);

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $futureDate = $today->copy()->addDays(40);

        $scheduledEvents = $this->getScheduledEvents($today, $futureDate);

        $availableDays = $this->calculateAvailableDays($today, $futureDate, $scheduledEvents, $totalServiceTime, $now);

        return response()->json($availableDays);
    }

    private function parseServiceIds(string $serviceIds): array
    {
        return array_map('intval', explode(',', $serviceIds));
    }

    private function calculateTotalServiceTime(array $serviceIds): int
    {
        return Service::whereIn('id', $serviceIds)->sum('duration');
    }

    private function getScheduledEvents(Carbon $startDate, Carbon $endDate)
    {
        return Scheduling::whereBetween('start_time', [$startDate, $endDate])
            ->where('status', 'Aguardando')
            ->get();
    }

    private function calculateAvailableDays(Carbon $startDate, Carbon $endDate, $scheduledEvents, int $totalServiceTime, Carbon $now): array
    {
        $availableDays = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $workingHours = WorkingHours::where('weekday', $date->dayOfWeek)->first();

            if ($workingHours) {
                $openTime = Carbon::parse($workingHours->open_time)->setDate($date->year, $date->month, $date->day);
                $closeTime = Carbon::parse($workingHours->close_time)->setDate($date->year, $date->month, $date->day);

                if ($date->isSameDay($now)) {
                    $openTime = max($now, $openTime);
                }

                $eventsOnDay = $scheduledEvents->filter(function ($event) use ($date) {
                    return Carbon::parse($event->start_time)->isSameDay($date);
                });

                $availableSlots = $this->calculateAvailableSlots($openTime, $closeTime, $eventsOnDay, $totalServiceTime);

                if (!empty($availableSlots)) {
                    $availableDays[] = $this->formatAvailableDay($date, $availableSlots);
                }
            }
        }

        return $availableDays;
    }

    private function calculateAvailableSlots(Carbon $openTime, Carbon $closeTime, $events, int $totalServiceTime): array
    {
        $availableSlots = [];

        $events = $events->sortBy('start_time');
        $maxEndTime = $closeTime->copy()->addMinutes(20);

        $lastEndTime = $openTime->copy();

        foreach ($events as $event) {
            $eventStartTime = Carbon::parse($event->start_time);
            $eventEndTime = Carbon::parse($event->end_time);

            if ($lastEndTime->diffInMinutes($eventStartTime) >= $totalServiceTime) {
                $availableSlots = array_merge($availableSlots, $this->getIntervals($lastEndTime, $eventStartTime, $totalServiceTime, $closeTime));
            }

            $lastEndTime = $eventEndTime;
        }

        if ($lastEndTime->diffInMinutes($closeTime) >= $totalServiceTime) {
            $availableSlots = array_merge($availableSlots, $this->getIntervals($lastEndTime, $closeTime, $totalServiceTime, $closeTime));
        }

        return $availableSlots;
    }

    private function getIntervals(Carbon $start, Carbon $end, int $duration, Carbon $closeTime): array
    {
        $intervals = [];

        // Corrigir para garantir que os intervalos comecem a partir do horário de abertura
        while ($start->copy()->addMinutes($duration)->lessThanOrEqualTo($end) && $start->lessThanOrEqualTo($closeTime)) {
            if ($start->copy()->addMinutes($duration)->lessThanOrEqualTo($closeTime)) {
                $intervals[] = $start->format('H:i:s');
            }
            $start->addMinutes($duration);
        }

        return $intervals;
    }

    private function formatAvailableDay(Carbon $date, array $availableSlots): array
    {
        return [
            'start' => $date->toDateString(),
            'end' => $date->toDateString(),
            'overlap' => true,
            'display' => 'background',
            'color' => '#B4FF4C',
            'allTimeAvailable' => $availableSlots,
        ];
    }

    public function schedule(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:15',
            'schedulingData' => 'required|date_format:d/m/Y H:i:s',
            'id_services' => 'required|string',
        ]);

        $servicesSelected = $this->parseServiceIds($validatedData['id_services']);
        $totalServiceTime = $this->calculateTotalServiceTime($servicesSelected);

        $startDateTime = Carbon::createFromFormat('d/m/Y H:i:s', $validatedData['schedulingData']);
        $endDateTime = (clone $startDateTime)->addMinutes($totalServiceTime);

        $data = $this->prepareSchedulingData($validatedData, $startDateTime, $endDateTime);

        DB::beginTransaction();

        try {
            $scheduling = Scheduling::create($data);
            $this->createServiceSchedulingRecords($scheduling->id, $servicesSelected);

            DB::commit();

            return response()->json([
                'message' => 'Seu agendamento foi salvo com sucesso!',
                'type' => 'success'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating scheduling: ', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Erro ao salvar o agendamento, tente novamente mais tarde.',
                'type' => 'error'
            ], 500);
        }
    }

    private function prepareSchedulingData(array $validatedData, Carbon $startDateTime, Carbon $endDateTime): array
    {
        return [
            'name' => $validatedData['name'],
            'whats' => $validatedData['whatsapp'],
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'status' => 'Aguardando'
        ];
    }

    private function createServiceSchedulingRecords(int $schedulingId, array $servicesSelected)
    {
        foreach ($servicesSelected as $serviceId) {
            ServiceScheduling::create([
                'scheduling_id' => $schedulingId,
                'service_id' => $serviceId,
            ]);
        }
    }
}
