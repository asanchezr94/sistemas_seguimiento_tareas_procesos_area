<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'task_date',
        'due_date',
        'resolution_date',
        'minutes_spent',
        'due_alerted_at',
        'task_name',
        'task_detail',
        'category',
        'priority',
        'status',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->latest();
    }
}
