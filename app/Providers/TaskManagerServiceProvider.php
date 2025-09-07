<?php

declare(strict_types=1);

namespace Modules\TaskManager\Providers;

use App\Enums\Hooks\AdminFilterHook;
use App\Services\MenuService\AdminMenuItem;
use App\Support\Facades\Hook;
use App\Support\HookManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\TaskManager\Enums\Hooks\TaskHook;
use Modules\TaskManager\Models\Task;
use Modules\TaskManager\Policies\TaskPolicy;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TaskManagerServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'TaskManager';

    protected string $nameLower = 'taskmanager';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerPolicies();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        $this->app->booted(function () {
            Hook::addFilter(AdminFilterHook::ADMIN_MENU_GROUPS_BEFORE_SORTING, [$this, 'addTaskManagerMenu']);
            $this->registerTaskHooks();
        });
    }

    public function addTaskManagerMenu(array $groups): array
    {
        $childMenusItems = [
            (new AdminMenuItem())->setAttributes([
                'label' => __('Tasks'),
                'route' => route('admin.tasks.index'),
                'active' => Route::is('admin.tasks.index') || Route::is('admin.tasks.edit'),
                'priority' => 1,
                'id' => 'tasks_manager_index',
                'permissions' => ['task.view'],
            ]),
            (new AdminMenuItem())->setAttributes([
                'label' => __('New Task'),
                'route' => route('admin.tasks.create'),
                'active' => Route::is('admin.tasks.create'),
                'priority' => 2,
                'id' => 'tasks_manager_create',
                'permissions' => ['task.create'],
            ]),
        ];

        $adminMenuItem = (new AdminMenuItem())->setAttributes([
            'label' => __('Task Manager'),
            'icon' => 'lucide:list-todo',
            'route' => route('admin.tasks.index'),
            'active' => Route::is('admin.tasks.*'),
            'id' => 'task-manager',
            'priority' => 21,
            'permissions' => ['task.view', 'task.create', 'task.edit', 'task.delete'],
            'children' => $childMenusItems,
        ]);

        $groups[__('Main')][] = $adminMenuItem;

        return $groups;
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register the module policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Task::class, TaskPolicy::class);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower . '.' . $config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace') . '\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }

    /**
     * Register task hooks for Task Manager module.
     */
    protected function registerTaskHooks(): void
    {
        $hookManager = app(HookManager::class);

        // Example: Handle task creation
        $hookManager->addAction(TaskHook::CREATED, function ($task) {
            // Send notification when task is created
        });

        // Example: Handle task updates
        $hookManager->addAction(TaskHook::UPDATED, function ($task) {
            // Log task update activity
        });

        // Example: Handle task completion
        $hookManager->addAction(TaskHook::COMPLETED, function ($task) {
            // Send completion notification or update metrics
        });

        // Example: Handle task assignment
        $hookManager->addAction(TaskHook::ASSIGNED, function ($task, $user) {
            // Notify assigned user
        });

        // Example: Filter task status options
        $hookManager->addFilter(TaskHook::STATUS_OPTIONS, function ($statuses) {
            // Add custom status options for tasks
            return $statuses;
        });

        // Example: Filter priority options
        $hookManager->addFilter(TaskHook::PRIORITY_OPTIONS, function ($priorities) {
            // Add custom priority options for tasks
            return $priorities;
        });

        // Example: Filter assignable users
        $hookManager->addFilter(TaskHook::ASSIGNABLE_USERS, function ($users) {
            // Filter users who can be assigned to tasks
            return $users;
        });
    }
}
