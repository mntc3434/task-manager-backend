<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class TaskService
{
    protected $storageFile = 'tasks.json';

    public function all()
    {
        if (!Storage::exists($this->storageFile)) {
            return [];
        }

        $tasks = json_decode(Storage::get($this->storageFile), true);
        return $tasks;
    }

    public function save(array $tasks)
    {
        Storage::put($this->storageFile, json_encode($tasks, JSON_PRETTY_PRINT));
    }

    public function add(array $task)
    {
        $tasks = $this->all();
        $task['id'] = uniqid();
        $task['completed'] = false;
        $task['created_at'] = now()->toDateTimeString();
        $task['updated_at'] = now()->toDateTimeString();
        $tasks[] = $task;
        $this->save($tasks);
        return $task;
    }

    public function update(string $id, array $data)
    {
        $tasks = $this->all();
        $updated = false;

        foreach ($tasks as &$task) {
            if ($task['id'] == $id) {
                $task = array_merge($task, $data);
                $task['updated_at'] = now()->toDateTimeString();
                $updated = true;
                break;
            }
        }

        if ($updated) {
            $this->save($tasks);
            return $tasks[array_search($id, array_column($tasks, 'id'))];
        }

        return null;
    }

    public function delete(string $id)
    {
        $tasks = $this->all();
        $initialCount = count($tasks);
        
        $tasks = array_filter($tasks, function ($task) use ($id) {
            return $task['id'] != $id;
        });

        if (count($tasks) < $initialCount) {
            $this->save($tasks);
            return true;
        }

        return false;
    }

    public function find(string $id)
    {
        $tasks = $this->all();
        foreach ($tasks as $task) {
            if ($task['id'] == $id) {
                return $task;
            }
        }
        return null;
    }
}