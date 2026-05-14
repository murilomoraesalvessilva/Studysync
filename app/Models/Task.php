<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['topic_id', 'user_id', 'group_id', 'status', 'deadline'];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function selfAssessment()
    {
        return $this->hasOne(SelfAssessment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'     => 'Pendente',
            'in_progress' => 'Em andamento',
            'done'        => 'Concluído',
            default       => 'Pendente',
        };
    }
}