# TaskManager

This is a simple Task Manager Free module for Lara Dashboard. It allows you to create, update, and delete tasks, as well as manage task statuses.
Its an example official module for Lara Dashboard, showcasing how to create a module with CRUD operations.

## Youtube Tutorial
Step by step guide how to add a module in Lara Dashboard easily -

[![Watch the video](https://github.com/user-attachments/assets/8bbd5b0a-6039-4e2c-89c1-3c3c5763fa6b)](https://youtu.be/rUW4vCSjSiI)

or try direct URL - https://youtu.be/rUW4vCSjSiI

## Documentation
https://laradashboard.com/docs/how-to-build-a-full-featured-task-management-module-in-lara-dashboard-step-by-step-guide/

## Features
- Create, update, and delete tasks
- Manage task statuses (e.g., pending, completed)
- Sort tasks by various criteria
- Bulk delete tasks
- Responsive design for mobile and desktop
- User-friendly interface with modals for task creation and editing
- Integration with Lara Dashboard's module system
- Example of using Lara Dashboard's built-in features like notifications and modals

## Installation

To install the TaskManager module, follow these steps:
Run the command inside your Lara Dashboard project directory:

```bash
cd modules
git clone https://github.com/laradashboard/task-manager.git
cd ..
php artisan module:enable taskamanager
```

## Screenshots

**Task List:**
![Task List](/screenshots/01-task-list.png)

![Task List Sorting](/screenshots/02-task-sorting.png)

**Create Task:**
![Create Task](/screenshots/10-task-create.png)

**Edit Task:**
![Edit Task](/screenshots/20-task-edit.png)

**Task Delete:**
![Task Delete](/screenshots/30-task-delete.png)

**Task Bulk Delete:**
![Task Bulk Delete](/screenshots/40-task-bulk-delete.png)

## Versions
- V1.0 - with basic CRUD operations - https://github.com/laradashboard/task-manager/releases/tag/v1.0
- V2.0 - with datatable integration - https://github.com/laradashboard/task-manager/releases/tag/v2.0

## More

For more information, please refer to the [Lara Dashboard documentation](https://laradashboard.com).
