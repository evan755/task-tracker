<?php declare(strict_types=1);

namespace Evan755\TaskTracker;
class Task
{
    protected array $tasks = [];
    protected string $file = 'tasks.json';

    public function __construct()
    {
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
        $this->tasks = json_decode(file_get_contents($this->file), true);
    }

    public function create(string $description): void
    {
        $task = [
            'id' => $this->id(),
            'description' => trim($description),
            'status' => 'todo',
            'createdAt' => date('Y-m-d H:i:s', time()),
            'updatedAt' => date('Y-m-d H:i:s', time()),
        ];
        $this->tasks[] = $task;
        $this->storage();
    }

    public function update(int $id, string $description): void
    {
        foreach ($this->tasks as &$task) {
            if ($task['id'] === (int)$id) {
                $task['description'] = trim($description);
                $task['updatedAt'] = date('Y-m-d H:i:s', time());
                $this->storage();
            }
        }
    }

    public function delete(int $id): void
    {
        $tasks = array_filter($this->tasks, function ($task) use ($id) {
            return $task['id'] !== (int)$id;
        });
        $this->tasks = array_values($tasks);
        $this->storage();
    }

    public function status(int $id, string $status): void
    {
        foreach ($this->tasks as &$task) {
            if ($task['id'] === (int)$id) {
                $task['status'] = str_replace("mark-", "", $status);
                $task['updatedAt'] = date('Y-m-d H:i:s', time());
                $this->storage();
            }
        }
    }

    public function index(string $status): void
    {
        $tasks = $this->tasks;
        if ($status) {
            $filter = ['todo', 'in-progress', 'done'];
            if (!in_array($status, $filter)) {
                echo 'Error: Invalid filter criteria: ' . $status . PHP_EOL;
                echo 'Info：Available filter criteria: ' . implode(' , ', $filter) . PHP_EOL;
                return;
            }
            $tasks = array_filter($this->tasks, function ($task) use ($status) {
                return $task['status'] === $status;
            });
        }
        echo sprintf("%-4s %-12s %-30s %-20s %-20s", "ID", "Status", "Description", "Created At", "Updated At") . PHP_EOL;
        echo str_repeat("-", 90) . PHP_EOL;
        foreach ($tasks as $task) {
            echo sprintf("%-4d %-12s %-30s %-20s %-20s",
                    $task['id'],
                    $task['status'],
                    strlen($task['description']) > 28 ? substr($task['description'], 0, 25) . '...' : $task['description'],
                    $task['createdAt'],
                    $task['updatedAt']
                ) . PHP_EOL;
        }
    }

    protected function id(): int
    {
        if (count($this->tasks) < 1) {
            return 1;
        }
        return max(array_column($this->tasks, 'id')) + 1;
    }

    protected function storage(): bool
    {
        return file_put_contents($this->file, json_encode($this->tasks, JSON_PRETTY_PRINT)) !== false;
    }
}