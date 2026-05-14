<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'weekly_hours'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'creator_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withPivot('role', 'weekly_hours', 'joined_at')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'proposer_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function selfAssessments()
    {
        return $this->hasMany(SelfAssessment::class);
    }
}