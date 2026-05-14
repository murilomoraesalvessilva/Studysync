<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfAssessment extends Model
{
    protected $fillable = ['task_id', 'user_id', 'level'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'understood'     => 'Entendi',
            'partial'        => 'Mais ou menos',
            'not_understood' => 'Não entendi',
            default          => '—',
        };
    }
}