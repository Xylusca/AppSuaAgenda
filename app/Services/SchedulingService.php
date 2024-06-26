<?php

namespace App\Services;

use App\Models\Scheduling;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class SchedulingService
{
    public function updateStatuses()
    {
        try {
            $recordsToUpdate = Scheduling::whereDate('start_time', Carbon::yesterday())
                ->where('status', 'Aguardando')
                ->get();

            foreach ($recordsToUpdate as $record) {
                $this->logStatusUpdate($record->id, 'Aguardando', 'Concluído');
                $record->update(['status' => 'Concluído']);
            }

            Log::info('Atualização de status na tabela Scheduling realizada com sucesso.');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar status na tabela Scheduling: ' . $e->getMessage());
        }
    }

    private function logStatusUpdate($id, $oldStatus, $newStatus)
    {
        Log::info("Atualizando status do registro {$id} de '{$oldStatus}' para '{$newStatus}'");
    }
}
