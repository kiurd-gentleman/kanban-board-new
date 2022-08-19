<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->statuses()->with('tasks')->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:56'],
            'status_id' => ['required', 'exists:statuses,id']
        ]);

        $request->order = auth()->user()->tasks()->where('status_id' , 1)->count() + 1;

        return $request->user()
            ->tasks()
            ->create([
                'title' => $request->title,
                'status_id' => $request->status_id,
                'order' => $request->order,
            ]);
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'columns' => ['required', 'array']
        ]);

        foreach ($request->columns as $status) {
            foreach ($status['tasks'] as $i => $task) {

                $order = $i + 1;
                if ($task['status_id'] !== $status['id'] || $task['order'] !== $order) {
                    request()->user()->tasks()
                        ->find($task['id'])
                        ->update(['status_id' => $status['id'], 'order' => $order]);
                }
            }
        }

        return $request->user()->statuses()->with('tasks')->get();
    }
}
