<?php

declare(strict_types=1);

namespace Modules\TaskManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TaskManager\Database\Factories\TaskFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Modules\TaskManager\Observers\TaskObserver;

#[ObservedBy([TaskObserver::class])]
class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'created_by',
    ];

    protected static function newFactory(): TaskFactory
    {
        return TaskFactory::new();
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public static function statuses(): array
    {
        return [
            'pending' => __('Pending'),
            'in_progress' => __('In Progress'),
            'completed' => __('Completed'),
        ];
    }

    public static function priorities(): array
    {
        return [
            'low' => __('Low'),
            'medium' => __('Medium'),
            'high' => __('High'),
        ];
    }
}
