<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tasks = Task::where('user_id', $user->id)
            ->orderByRaw("FIELD(priority, 'Q1', 'Q2', 'Q3')")
            ->orderBy('due_date', 'asc')
            ->get();

        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:Q1,Q2,Q3',
            'due_date' => 'nullable|date',
        ]);

        Task::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function toggleStatus(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->update([
            'status' => $task->status === 'completed' ? 'pending' : 'completed',
        ]);

        return redirect()->back()->with('success', 'Status tugas diperbarui.');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function startTimer(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        // Stop any other running timers for this user first (standard Pomodoro/Focus behavior)
        Task::where('user_id', Auth::id())
            ->whereNotNull('timer_started_at')
            ->get()
            ->each(function ($runningTask) {
                $elapsed = now()->diffInSeconds($runningTask->timer_started_at);
                $runningTask->update([
                    'total_seconds' => $runningTask->total_seconds + $elapsed,
                    'timer_started_at' => null,
                ]);
            });

        $task->update([
            'timer_started_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Timer dimulai.');
    }

    public function stopTimer(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        if ($task->timer_started_at) {
            $elapsed = now()->diffInSeconds($task->timer_started_at);
            $task->update([
                'total_seconds' => $task->total_seconds + $elapsed,
                'timer_started_at' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Timer dihentikan.');
    }
}
