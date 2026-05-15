<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Task;

class DistributionService
{
    /**
     * Distribui os temas do grupo entre os membros de forma proporcional
     * à disponibilidade semanal de cada um.
     */
    public function distribute(Group $group): void
    {
        // Remove tarefas existentes (redistribuição)
        Task::where('group_id', $group->id)->delete();

        $topics  = $group->topics()->orderByDesc('weight')->get();
        $members = $group->members()->withPivot('weekly_hours')->get();

        if ($topics->isEmpty() || $members->isEmpty()) {
            return;
        }

        $totalHours = $members->sum(fn($m) => $m->pivot->weekly_hours);

        // Calcula a carga total em peso
        $totalWeight = $topics->sum('weight');

        // Quota de peso por hora de disponibilidade
        $weightPerHour = $totalHours > 0 ? $totalWeight / $totalHours : 1;

        // Inicializa a carga atual de cada membro
        $loads = $members->mapWithKeys(fn($m) => [
            $m->id => [
                'user'     => $m,
                'capacity' => $m->pivot->weekly_hours * $weightPerHour,
                'load'     => 0,
            ]
        ])->toArray();

        $tasks = [];

        foreach ($topics as $topic) {
            // Atribui ao membro com menor carga relativa
            $bestMemberId = collect($loads)
                ->sortBy(fn($l) => $l['load'] / max($l['capacity'], 1))
                ->keys()
                ->first();

            $loads[$bestMemberId]['load'] += $topic->weight;

            $tasks[] = [
                'topic_id'   => $topic->id,
                'user_id'    => $bestMemberId,
                'group_id'   => $group->id,
                'status'     => 'pending',
                'deadline'   => $group->exam_date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Task::insert($tasks);

        // Avança o status do grupo
        if ($group->status === 'open') {
            $group->update(['status' => 'negotiating']);
        }
    }
}