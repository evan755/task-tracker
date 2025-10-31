<?php declare(strict_types=1);

namespace Evan755\TaskTracker;
class Main
{
    protected int $count = 0;
    protected array $params = [];

    public function __construct()
    {
        $this->count = $_SERVER['argc'];
        $this->params = $_SERVER['argv'];
    }

    public function run(): void
    {
        if (PHP_SAPI !== 'cli') {
            echo "Info: This script requires a command-line interface to run." . PHP_EOL;
        }
        if ($this->count < 2) {
            $this->help();
            exit(1);
        }
        $task = new Task();
        $command = $this->params[1];
        switch ($command) {
            case 'list':
                $filter = $this->count >= 3 ? $this->params[2] : '';
                $task->index($filter);
                break;

            case 'create':
                if ($this->count < 3) {
                    echo "Error: Please provide a task description." . PHP_EOL;
                    exit(1);
                }
                $task->create($this->params[2]);
                break;

            case 'update':
                if ($this->count < 4) {
                    echo "Error: Missing task ID or new description." . PHP_EOL;
                    exit(1);
                }
                $task->update((int)$this->params[2], $this->params[3]);
                break;

            case 'delete':
                if ($this->count < 3) {
                    echo "Error: Task ID required." . PHP_EOL;
                    exit(1);
                }
                $task->delete((int)$this->params[2]);
                break;

            case 'mark-in-progress':
                if ($this->count < 3) {
                    echo "Error: Task ID required to mark as \'in-progress\'" . PHP_EOL;
                    exit(1);
                }
                $task->status((int)$this->params[2], $this->params[1]);
                break;

            case 'mark-done':
                if ($this->count < 3) {
                    echo "Error: Task ID required to mark as \'done\'" . PHP_EOL;
                    exit(1);
                }
                $task->status((int)$this->params[2], $this->params[1]);
                break;

            case 'help':
                $this->help();
                break;

            default:
                echo "Error: Command not found " . $command . PHP_EOL;
                echo "Info: 'task-tracker help' Available Commands" . PHP_EOL;
                exit(1);
        }
    }

    protected function help(): void
    {
        echo "# Task Tracker CLI" . PHP_EOL;
        echo "## Create Task: ./task-tracker add \"Description\"" . PHP_EOL;
        echo "## Update Task: ./task-tracker update ID \"Description\"" . PHP_EOL;
        echo "## Delete Task: ./task-tracker delete ID" . PHP_EOL;
        echo "## Task List: ./task-tracker list [todo|in-progress|done]" . PHP_EOL;
        echo "## Mark Task: ./task-tracker mark-done|mark-in-progress ID" . PHP_EOL;
        echo "## Show Help: ./task-tracker help" . PHP_EOL;
    }
}