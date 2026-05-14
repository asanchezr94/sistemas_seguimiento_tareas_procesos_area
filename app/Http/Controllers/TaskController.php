<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    private const CATEGORIES = [
        'opa',
        'visionamos',
        'app movil',
        'mesa de ayuda opa',
        'mesa de ayuda visionamos',
        'configuraciones',
        'proveedores',
        'tesoreria',
        'comercial',
        'bienestar',
        'credito',
        'cartera',
        'sistemas',
        'contabilidad',
        'riesgos',
        'gerencia',
        'soporte',
        'fallas masivas',
        'proyecto',
        'mejora preventiva',
        'automatizacion',
    ];
    private const NO_DUE_DATE_CATEGORIES = ['proyecto', 'mejora preventiva', 'automatizacion'];

    private const PRIORITIES = ['baja', 'media', 'alta'];
    private const STATUSES = ['pendiente', 'en proceso', 'escalamiento', 'terminado'];

    public function index(Request $request)
    {
        $this->createDueDateAlerts($request->user()->id);

        $statusFilter = $request->query('status');
        $monthFilter = $request->query('month');
        $categoryFilter = $request->query('category');
        $query = Task::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('task_date')
            ->orderByDesc('id');

        if ($statusFilter && in_array($statusFilter, self::STATUSES, true)) {
            $query->where('status', $statusFilter);
        }
        if ($categoryFilter && in_array($categoryFilter, self::CATEGORIES, true)) {
            $query->where('category', $categoryFilter);
        }
        if ($monthFilter) {
            try {
                $monthDate = Carbon::createFromFormat('Y-m', $monthFilter);
                $query->whereYear('task_date', $monthDate->year)
                    ->whereMonth('task_date', $monthDate->month);
            } catch (\Exception $e) {
                // Ignore invalid month filters.
            }
        }

        return view('tasks.index', [
            'tasks' => $query->paginate(20)->withQueryString(),
            'statusFilter' => $statusFilter,
            'monthFilter' => $monthFilter,
            'categoryFilter' => $categoryFilter,
            'categories' => self::CATEGORIES,
            'priorities' => self::PRIORITIES,
            'statuses' => self::STATUSES,
            'unreadNotificationsCount' => $request->user()->notifications()->whereNull('read_at')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_name' => ['required', 'string', 'max:255'],
            'task_detail' => ['required', 'string'],
            'category' => ['required', 'in:' . implode(',', self::CATEGORIES)],
            'priority' => ['required', 'in:' . implode(',', self::PRIORITIES)],
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['user_name'] = $request->user()->name;
        $validated['task_date'] = now()->toDateString();
        $daysByPriority = ['baja' => 5, 'media' => 3, 'alta' => 1];
        $validated['due_date'] = in_array($validated['category'], self::NO_DUE_DATE_CATEGORIES, true)
            ? null
            : now()->addDays($daysByPriority[$validated['priority']])->toDateString();
        $validated['resolution_date'] = $validated['status'] === 'terminado'
            ? now()->toDateString()
            : null;

        $task = Task::create($validated);

        UserNotification::create([
            'user_id' => $request->user()->id,
            'title' => 'Nueva tarea registrada',
            'message' => 'Se registró la tarea #' . $task->id . ': ' . $task->task_name,
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Tarea registrada correctamente.');
    }

    public function show(Request $request, int $taskId)
    {
        $task = Task::query()
            ->where('user_id', $request->user()->id)
            ->with('comments')
            ->findOrFail($taskId);

        return view('tasks.show', [
            'task' => $task,
            'priorities' => self::PRIORITIES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function indicators(Request $request)
    {
        $userId = $request->user()->id;
        $monthFilter = $request->query('month', now()->format('Y-m'));

        try {
            $monthDate = Carbon::createFromFormat('Y-m', $monthFilter);
        } catch (\Exception $e) {
            $monthDate = now();
            $monthFilter = $monthDate->format('Y-m');
        }

        $baseQuery = Task::query()->where('user_id', $userId);
        $monthQuery = Task::query()
            ->where('user_id', $userId)
            ->whereYear('task_date', $monthDate->year)
            ->whereMonth('task_date', $monthDate->month);

        $statusCountsMonth = [
            'pendiente' => (clone $monthQuery)->where('status', 'pendiente')->count(),
            'en proceso' => (clone $monthQuery)->where('status', 'en proceso')->count(),
            'escalamiento' => (clone $monthQuery)->where('status', 'escalamiento')->count(),
            'terminado' => (clone $monthQuery)->where('status', 'terminado')->count(),
        ];

        $specialCategories = ['proyecto', 'automatizacion', 'mejora preventiva', 'soporte'];
        $categoryStatsAll = [];
        $categoryStatsMonth = [];
        foreach ($specialCategories as $category) {
            $categoryStatsAll[$category] = [
                'creadas' => (clone $baseQuery)->where('category', $category)->count(),
                'terminadas' => (clone $baseQuery)->where('category', $category)->where('status', 'terminado')->count(),
                'minutos_terminadas' => (int) ((clone $baseQuery)
                    ->where('category', $category)
                    ->where('status', 'terminado')
                    ->sum('minutes_spent')),
            ];
            $categoryStatsMonth[$category] = [
                'creadas' => (clone $monthQuery)->where('category', $category)->count(),
                'terminadas' => (clone $monthQuery)->where('category', $category)->where('status', 'terminado')->count(),
                'minutos_terminadas' => (int) ((clone $monthQuery)
                    ->where('category', $category)
                    ->where('status', 'terminado')
                    ->sum('minutes_spent')),
            ];
        }

        $operationalCategories = ['fallas masivas', 'opa', 'visionamos', 'app movil', 'configuraciones', 'proveedores'];
        $operationalStatsAll = [];
        $operationalStatsMonth = [];
        foreach ($operationalCategories as $category) {
            $operationalStatsAll[$category] = [
                'creadas' => (clone $baseQuery)->where('category', $category)->count(),
                'terminadas' => (clone $baseQuery)->where('category', $category)->where('status', 'terminado')->count(),
                'minutos_terminadas' => (int) ((clone $baseQuery)
                    ->where('category', $category)
                    ->where('status', 'terminado')
                    ->sum('minutes_spent')),
            ];
            $operationalStatsMonth[$category] = [
                'creadas' => (clone $monthQuery)->where('category', $category)->count(),
                'terminadas' => (clone $monthQuery)->where('category', $category)->where('status', 'terminado')->count(),
                'minutos_terminadas' => (int) ((clone $monthQuery)
                    ->where('category', $category)
                    ->where('status', 'terminado')
                    ->sum('minutes_spent')),
            ];
        }

        $specialTasksMonth = (clone $monthQuery)
            ->whereIn('category', $specialCategories)
            ->orderByDesc('task_date')
            ->orderByDesc('id')
            ->get([
                'id',
                'task_name',
                'category',
                'status',
                'task_date',
                'resolution_date',
            ]);

        $slaBaseMonth = (clone $monthQuery)
            ->where('status', 'terminado')
            ->whereNotNull('due_date')
            ->whereNotNull('resolution_date');
        $slaOnTimeMonth = (clone $slaBaseMonth)->whereColumn('resolution_date', '<=', 'due_date')->count();
        $slaLateMonth = (clone $slaBaseMonth)->whereColumn('resolution_date', '>', 'due_date')->count();
        $slaTotalMonth = $slaOnTimeMonth + $slaLateMonth;
        $slaRateMonth = $slaTotalMonth > 0 ? round(($slaOnTimeMonth / $slaTotalMonth) * 100, 2) : 0;

        $slaBaseAll = (clone $baseQuery)
            ->where('status', 'terminado')
            ->whereNotNull('due_date')
            ->whereNotNull('resolution_date');
        $slaOnTimeAll = (clone $slaBaseAll)->whereColumn('resolution_date', '<=', 'due_date')->count();
        $slaLateAll = (clone $slaBaseAll)->whereColumn('resolution_date', '>', 'due_date')->count();
        $slaTotalAll = $slaOnTimeAll + $slaLateAll;
        $slaRateAll = $slaTotalAll > 0 ? round(($slaOnTimeAll / $slaTotalAll) * 100, 2) : 0;

        return view('indicators.index', [
            'monthFilter' => $monthFilter,
            'monthLabel' => $monthDate->translatedFormat('F Y'),
            'totalMonth' => (clone $monthQuery)->count(),
            'statusCountsMonth' => $statusCountsMonth,
            'totalAll' => (clone $baseQuery)->count(),
            'categoryStatsAll' => $categoryStatsAll,
            'categoryStatsMonth' => $categoryStatsMonth,
            'operationalStatsAll' => $operationalStatsAll,
            'operationalStatsMonth' => $operationalStatsMonth,
            'specialTasksMonth' => $specialTasksMonth,
            'unreadNotificationsCount' => $request->user()->notifications()->whereNull('read_at')->count(),
            'slaOnTimeMonth' => $slaOnTimeMonth,
            'slaLateMonth' => $slaLateMonth,
            'slaTotalMonth' => $slaTotalMonth,
            'slaRateMonth' => $slaRateMonth,
            'slaOnTimeAll' => $slaOnTimeAll,
            'slaLateAll' => $slaLateAll,
            'slaTotalAll' => $slaTotalAll,
            'slaRateAll' => $slaRateAll,
        ]);
    }

    public function update(Request $request, int $taskId)
    {
        $task = Task::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($taskId);

        if ($task->status === 'terminado') {
            return redirect()->route('tasks.show', $task->id)
                ->withErrors(['status' => 'La tarea ya esta terminada y no permite cambios en sus datos.']);
        }

        $validated = $request->validate([
            'priority' => ['required', 'in:' . implode(',', self::PRIORITIES)],
            'status' => ['required', 'in:' . implode(',', self::STATUSES)],
            'minutes_spent' => ['nullable', 'integer', 'min:1'] ,
        ]);

        if ($validated['status'] === 'terminado' && !$request->filled('minutes_spent')) {
            return back()->withErrors([
                'minutes_spent' => 'Los minutos invertidos son obligatorios cuando la tarea esta terminada.',
            ])->withInput();
        }

        $daysByPriority = ['baja' => 5, 'media' => 3, 'alta' => 1];
        $validated['due_date'] = in_array($task->category, self::NO_DUE_DATE_CATEGORIES, true)
            ? null
            : Carbon::parse($task->task_date)->addDays($daysByPriority[$validated['priority']])->toDateString();
        $validated['resolution_date'] = $validated['status'] === 'terminado'
            ? ($task->resolution_date ?: now()->toDateString())
            : null;
        if ($validated['status'] !== 'terminado') {
            $validated['minutes_spent'] = null;
        }

        $task->update($validated);

        return redirect()->route('tasks.show', $task->id)
            ->with('success', 'Tarea actualizada correctamente.');
    }

    private function createDueDateAlerts(int $userId): void
    {
        $today = Carbon::today();

        $tasks = Task::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['pendiente', 'en proceso', 'escalamiento'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '=', $today)
            ->whereNull('due_alerted_at')
            ->get();

        foreach ($tasks as $task) {
            UserNotification::create([
                'user_id' => $userId,
                'title' => 'Tarea vence hoy',
                'message' => 'La tarea #' . $task->id . ' (' . $task->task_name . ') vence hoy ' . $today->toDateString() . '.',
            ]);

            $task->update(['due_alerted_at' => now()]);
        }
    }
}

