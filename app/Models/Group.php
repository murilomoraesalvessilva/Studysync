<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'subject', 'goal', 'exam_date', 'status', 'invite_token', 'creator_id'];

    protected $casts = [
        'exam_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Group $group) {
            $group->invite_token = Str::random(32);
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot('role', 'weekly_hours', 'joined_at')
            ->withTimestamps();
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function getInviteLinkAttribute(): string
    {
        return route('groups.join', $this->invite_token);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'        => 'Aberto',
            'negotiating' => 'Em negociação',
            'agreed'      => 'Acordo fechado',
            'concluded'   => 'Concluído',
            default       => 'Desconhecido',
        };
    }
}