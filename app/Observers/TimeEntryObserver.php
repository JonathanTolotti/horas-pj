<?php

namespace App\Observers;

use App\Models\OnCallPeriod;
use App\Models\TimeEntry;

class TimeEntryObserver
{
    /**
     * Handle the TimeEntry "created" event.
     */
    public function created(TimeEntry $timeEntry): void
    {
        $this->linkToOnCallPeriod($timeEntry);
    }

    /**
     * Handle the TimeEntry "updated" event.
     */
    public function updated(TimeEntry $timeEntry): void
    {
        // Se mudou data ou horarios, recalcular vinculo
        if ($timeEntry->wasChanged(['date', 'start_time', 'end_time'])) {
            $this->unlinkFromOnCallPeriod($timeEntry);
            $this->linkToOnCallPeriod($timeEntry);
        }
    }

    /**
     * Handle the TimeEntry "deleted" event.
     */
    public function deleted(TimeEntry $timeEntry): void
    {
        $this->unlinkFromOnCallPeriod($timeEntry);
    }

    /**
     * Vincula o lancamento a um periodo de sobreaviso se aplicavel.
     */
    protected function linkToOnCallPeriod(TimeEntry $entry): void
    {
        // Buscar periodos de sobreaviso que podem conter este lancamento
        $periods = OnCallPeriod::forUser($entry->user_id)
            ->where('start_datetime', '<=', $entry->date->format('Y-m-d') . ' 23:59:59')
            ->where('end_datetime', '>=', $entry->date->format('Y-m-d') . ' 00:00:00')
            ->get();

        if ($periods->isEmpty()) {
            return;
        }

        // Normalizar o formato de hora (garantir que seja H:i)
        $startTime = substr($entry->start_time, 0, 5);
        $endTime = substr($entry->end_time, 0, 5);

        foreach ($periods as $period) {
            $overlapHours = $period->getOverlapHours(
                $entry->date->format('Y-m-d'),
                $startTime,
                $endTime
            );

            if ($overlapHours > 0) {
                // Usar updateQuietly para evitar loop infinito
                $entry->updateQuietly(['on_call_period_id' => $period->id]);
                $period->recalculateHours();
                break;
            }
        }
    }

    /**
     * Desvincula o lancamento do periodo de sobreaviso.
     */
    protected function unlinkFromOnCallPeriod(TimeEntry $entry): void
    {
        if (!$entry->on_call_period_id) {
            return;
        }

        $period = OnCallPeriod::find($entry->on_call_period_id);

        // Usar updateQuietly para evitar loop infinito
        $entry->updateQuietly(['on_call_period_id' => null]);

        if ($period) {
            $period->recalculateHours();
        }
    }
}
