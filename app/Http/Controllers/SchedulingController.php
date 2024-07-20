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
    /**
     * Fetches available days for scheduling appointments based on service duration,
     * working hours, and existing scheduled events.
     *
     * @param Request $request Incoming request containing service IDs.
     * @return JsonResponse Response with available days and times or error message.
     * @throws Exception If an unexpected error occurs.
     */
    public function availableDays(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'id_services' => 'required|string',
            ]);

            $servicesSelected = array_map('intval', explode(',', $validatedData['id_services']));
            $totalServiceTime = $this->calculateTotalServiceTime($servicesSelected);

            $now = Carbon::now();
            $today = $now->copy()->startOfDay();
            $futureDate = $today->copy()->addDays(40);

            $scheduledEvents = $this->getScheduledEvents($today, $futureDate);

            $availableDays = $this->calculateAvailableDays($today, $futureDate, $scheduledEvents, $totalServiceTime, $now);

            return response()->json($availableDays, 200);
        } catch (\Exception $e) {
            Log::error('Error query scheduling: ', ['error' => $e->getMessage()]);
            return response()->json($this->createMessageArray('Erro interno do servidor', 'danger'), 500);
        }
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

    /**
     * Calculates available days considering working hours, existing events,
     * and service duration.
     *
     * @param Carbon $startDate Start date of the availability check.
     * @param Carbon $endDate End date of the availability check.
     * @param Collection $scheduledEvents Collection of scheduled events.
     * @param int $totalServiceTime Total duration of the requested services (in minutes).
     * @param Carbon $now Current date and time.
     * @return array Array of available days with their details.
     */
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

            if ($eventStartTime >= $openTime) {
                if ($lastEndTime->diffInMinutes($eventStartTime) >= $totalServiceTime) {
                    $availableSlots = array_merge($availableSlots, $this->getIntervals($lastEndTime, $eventStartTime, $totalServiceTime, $closeTime));
                }

                $lastEndTime = $eventEndTime;
            }
        }

        if ($lastEndTime->diffInMinutes($closeTime) >= $totalServiceTime) {
            $availableSlots = array_merge($availableSlots, $this->getIntervals($lastEndTime, $closeTime, $totalServiceTime, $closeTime));
        }

        return $availableSlots;
    }

    private function getIntervals(Carbon $start, Carbon $end, int $duration, Carbon $closeTime): array
    {
        $intervals = [];

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

    /**
     * Schedules an appointment based on provided information and validates for conflicts.
     *
     * @param Request $request Incoming request containing appointment details.
     * @return JsonResponse Response with success or error message.
     * @throws Exception If an unexpected error occurs.
     */
    public function schedule(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'whatsapp' => 'required|string|max:15',
                'schedulingData' => 'required|date_format:d/m/Y H:i:s',
                'id_services' => 'required|string',
            ]);
            $servicesSelected = array_map('intval', explode(',', $validatedData['id_services']));

            $totalServiceTime = $this->calculateTotalServiceTime($servicesSelected);

            $startDateTime = Carbon::createFromFormat('d/m/Y H:i:s', $validatedData['schedulingData']);
            $endDateTime = $startDateTime->copy()->addMinutes($totalServiceTime);

            $data = $this->prepareSchedulingData($validatedData, $startDateTime, $endDateTime);

            DB::beginTransaction();

            // Verifica se já existe um agendamento com as mesmas datas
            $isDuplicate = Scheduling::where(['start_time' => $startDateTime, 'end_time' => $endDateTime])->exists();

            if ($isDuplicate) {
                $data_response = [
                    'message' => 'Já existe um registro com essas datas.',
                    'type' => 'warning '
                ];
            } else {
                $scheduling = Scheduling::create($data);
                $this->createServiceSchedulingRecords($scheduling->id, $servicesSelected);

                $data_response = [
                    'message' => 'Seu agendamento foi salvo com sucesso!',
                    'type' => 'success'
                ];
            }

            DB::commit();

            return response()->json($data_response, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating scheduling: ', ['error' => $e->getMessage()]);

            return response()->json($this->createMessageArray('Erro ao salvar o agendamento, tente novamente mais tarde.', 'danger'), 500);
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

    /**
     * Queries a booking based on the information provided and validates conflicts.
     *
     * @param Request $request Incoming request containing appointment details.
     * @return JsonResponse Response with success or error message.
     * @throws Exception If an unexpected error occurs.
     */
    public function checkAppointment(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'whatsapp' => 'required|string|max:15',
            ]);

            $scheduling = Scheduling::where('whats', $validatedData['whatsapp'])
                ->orderBy('start_time', 'desc')
                ->with('services_schedulings.service')
                ->limit(10)
                ->get();

            $scheduling->transform(function ($item) {
                $totalPrice = 0;

                foreach ($item->services_schedulings as $serviceScheduling) {
                    $totalPrice += $serviceScheduling->service->price;
                }

                return [
                    'name' => $item->name,
                    'whats' => $item->whats,
                    'start_time' =>  date('d/m/Y H:i', strtotime($item->start_time)),
                    'status' => $item->status,
                    'totalPrice' => number_format($totalPrice, 2, ',', '.'),
                ];
            });

            return response()->json($this->createMessageArray('Consulta realizada com sucesso', 'success', $scheduling), 200);
        } catch (\Exception $e) {
            Log::error('Error query scheduling: ', ['error' => $e->getMessage()]);
            return response()->json($this->createMessageArray('Erro interno do servidor', 'danger'), 500);
        }
    }

    private function createMessageArray(string $msg, string $type = 'success', $data = null): array
    {
        $data_response = [
            'message' => $msg,
            'type' => $type,
        ];

        if ($data !== null) {
            $data_response['data'] = $data;
        }

        return $data_response;
    }
}
