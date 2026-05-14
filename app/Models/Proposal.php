<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'proposer_id', 'type', 'description', 'status', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposer_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'task_swap'          => 'Troca de tarefa',
            'topic_split'        => 'Divisão de tema',
            'overload'           => 'Sinalização de sobrecarga',
            'deadline_extension' => 'Extensão de prazo',
            default              => 'Proposta',
        };
    }

    public function getYesVotesAttribute(): int
    {
        return $this->votes->where('vote', true)->count();
    }

    public function getNoVotesAttribute(): int
    {
        return $this->votes->where('vote', false)->count();
    }

    public function userVoted(int $userId): ?Vote
    {
        return $this->votes->firstWhere('user_id', $userId);
    }
}