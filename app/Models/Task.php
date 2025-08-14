<?php

declare(strict_types=1);

namespace Modules\TaskManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TaskManager\Database\Factories\TaskFactory;
use App\Models\User;
use App\Concerns\QueryBuilderTrait;

class Task extends Model
{
    use HasFactory;
    use QueryBuilderTrait;

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

    /**
     * Get searchable columns for the model.
     */
    protected function getSearchableColumns(): array
    {
        return ['title', 'status', 'priority'];
    }

    /**
     * Get columns that should be excluded from sorting.
     */
    protected function getExcludedSortColumns(): array
    {
        return [];
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
