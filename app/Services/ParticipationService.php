<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;

class ParticipationService
{
    /**
     * Calcula o indicador de participação de um membro em um grupo.
     * Retorna valor de 0 a 100.
     */
    public function calculate(User $user, Group $group): int
    {
        $score = 0;
        $max   = 0;

        // Tarefas concluídas no prazo (peso 40)
        $tasks = $group->tasks()->where('user_id', $user->id)->get();
        if ($tasks->isNotEmpty()) {
            $max += 40;
            $doneTasks = $tasks->where('status', 'done')->count();
            $score    += (int) round(($doneTasks / $tasks->count()) * 40);
        }

        // Votos em propostas (peso 20)
        $proposals = $group->proposals()->where('status', '!=', 'open')->count();
        if ($proposals > 0) {
            $max += 20;
            $voted  = $user->votes()->whereIn('proposal_id',
                $group->proposals->pluck('id')
            )->count();
            $score += (int) round(min($voted / $proposals, 1) * 20);
        }

        // Autoavaliações preenchidas (peso 20)
        if ($tasks->isNotEmpty()) {
            $max += 20;
            $assessed = $user->selfAssessments()
                ->whereIn('task_id', $tasks->pluck('id'))
                ->count();
            $score += (int) round(min($assessed / $tasks->count(), 1) * 20);
        }

        // Mensagens enviadas no chat (peso 20)
        $max   += 20;
        $msgs   = $group->messages()->where('user_id', $user->id)->count();
        $score += min($msgs * 4, 20); // até 5 mensagens = 20 pontos

        return $max > 0 ? (int) round(($score / $max) * 100) : 0;
    }
}