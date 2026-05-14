<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'title', 'difficulty', 'weight'];

    protected static function booted(): void
    {
        static::saving(function (Topic $topic) {
            $topic->weight = match($topic->difficulty) {
                'easy'   => 1,
                'medium' => 2,
                'hard'   => 3,
                default  => 2,
            };
        });
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty) {
            'easy'   => 'Fácil',
            'medium' => 'Médio',
            'hard'   => 'Difícil',
            default  => 'Médio',
        };
    }
}