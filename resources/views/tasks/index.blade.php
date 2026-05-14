<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasking</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 24px; color: #1f2937; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: #fff; border-radius: 10px; padding: 18px; margin-bottom: 18px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
        h1 { margin: 0 0 12px; }
        .brand-head { display: flex; align-items: flex-end; gap: 12px; margin: 0 0 6px; flex-wrap: wrap; }
        .brand-logo { max-height: 96px; width: auto; display: block; }
        .brand-title { font-size: 28px; font-weight: 700; color: #000; padding-bottom: 6px; }
        .grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0,1fr)); }
        .grid .full { grid-column: span 2; }
        label { font-size: 13px; font-weight: 700; display: block; margin-bottom: 6px; }
        input, select, textarea, button { width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; font-size: 14px; box-sizing: border-box; }
        textarea { min-height: 90px; resize: vertical; }
        button { background: #0f766e; color: #fff; border: none; cursor: pointer; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; table-layout: fixed; }
        th, td { border-bottom: 1px solid #e5e7eb; text-align: left; padding: 10px 8px; }
        th { background: #f9fafb; }
        td { word-wrap: break-word; }
        .detail-cell {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .toolbar { display: flex; gap: 10px; align-items: end; }
        .toolbar > div { min-width: 220px; }
        .alert { padding: 10px; border-radius: 8px; margin-bottom: 10px; }
        .success { background: #dcfce7; color: #166534; }
        .errors { background: #fee2e2; color: #991b1b; }
        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
            .grid .full { grid-column: span 1; }
            .toolbar { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="brand-head">
        <img class="brand-logo" src="{{ asset('images/logo-cotrasena.png') }}" alt="COTRASENA">
        <div class="brand-title">- Gestor de tareas por área</div>
    </div>
    <h1>Tareas</h1>
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin:10px 0 18px;flex-wrap:wrap;">
        <div><strong>Usuario:</strong> {{ auth()->user()->name }} ({{ auth()->user()->role }})</div>
        <div style="display:flex;align-items:center;gap:10px;">
            <a href="{{ route('tasks.index') }}" style="background:#0f766e;color:#fff;padding:10px 14px;border-radius:8px;text-decoration:none;font-weight:700;">Inicio</a>
            <a href="{{ route('indicators.index') }}" style="background:#0f766e;color:#fff;padding:10px 14px;border-radius:8px;text-decoration:none;font-weight:700;">Indicadores</a>
            <a href="{{ route('notifications.index') }}" style="background:#ef4444;color:#fff;padding:10px 14px;border-radius:8px;text-decoration:none;font-weight:700;">Notificaciones ({{ $unreadNotificationsCount }})</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:auto;padding:10px 14px;">Cerrar sesion</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Nueva tarea</h2>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert errors">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="grid">
                <div>
                    <label>Fecha</label>
                    <input value="{{ now()->toDateString() }}" readonly>
                </div>
                <div>
                    <label for="task_name">Nombre de la tarea</label>
                    <input id="task_name" name="task_name" value="{{ old('task_name') }}" required>
                </div>
                <div>
                    <label for="due_date">Fecha de vencimiento</label>
                    <input id="due_date" type="date" value="{{ old('due_date') }}" readonly>
                </div>
                <div>
                    <label for="category">Categoria</label>
                    <select id="category" name="category" required>
                        <option value="">Seleccione...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="priority">Prioridad</label>
                    <select id="priority" name="priority" required>
                        <option value="">Seleccione...</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority }}" @selected(old('priority') === $priority)>{{ ucfirst($priority) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status">Estado</label>
                    <select id="status" name="status" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', 'pendiente') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="full">
                    <label for="task_detail">Detalle de la tarea</label>
                    <textarea id="task_detail" name="task_detail" required>{{ old('task_detail') }}</textarea>
                </div>
                <div class="full">
                    <button type="submit">Registrar tarea</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Tareas registradas</h2>
        <form class="toolbar" method="GET" action="{{ route('tasks.index') }}">
            <div>
                <label for="status_filter">Filtrar por estado</label>
                <select id="status_filter" name="status">
                    <option value="">Todos</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected($statusFilter === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="month_filter">Filtrar por mes</label>
                <input id="month_filter" type="month" name="month" value="{{ $monthFilter }}">
            </div>
            <div>
                <label for="category_filter">Filtrar por categoria</label>
                <select id="category_filter" name="category">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected($categoryFilter === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit">Aplicar filtro</button>
            </div>
        </form>

        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Vence</th>
                <th>Solucion</th>
                <th>Usuario</th>
                <th>Tarea</th>
                <th>Detalle</th>
                <th>Categoria</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Minutos</th>
                <th>Dias resolucion</th>
                <th>Cumplimiento</th>
                <th>Accion</th>
            </tr>
            </thead>
            <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->task_date }}</td>
                    <td>{{ $task->due_date ?? '-' }}</td>
                    <td>{{ $task->resolution_date ?? '-' }}</td>
                    <td>{{ $task->user_name }}</td>
                    <td>{{ $task->task_name }}</td>
                    <td class="detail-cell" title="{{ $task->task_detail }}">{{ \Illuminate\Support\Str::limit($task->task_detail, 60) }}</td>
                    <td>{{ $task->category }}</td>
                    <td>{{ ucfirst($task->priority) }}</td>
                    <td>{{ ucfirst($task->status) }}</td>
                    <td>{{ $task->minutes_spent ?? '-' }}</td>
                    <td>
                        @if($task->resolution_date)
                            {{ \Carbon\Carbon::parse($task->task_date)->diffInDays(\Carbon\Carbon::parse($task->resolution_date)) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($task->status === 'terminado' && $task->due_date && $task->resolution_date)
                            @if(\Carbon\Carbon::parse($task->resolution_date)->lte(\Carbon\Carbon::parse($task->due_date)))
                                A tiempo
                            @else
                                Fuera de tiempo
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('tasks.show', $task->id) }}" style="display:inline-block;background:#0f766e;color:#fff;padding:8px 12px;border-radius:8px;text-decoration:none;font-weight:700;">
                            Ver / Actualizar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="14">No hay tareas registradas.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:12px;">
            {{ $tasks->links() }}
        </div>
    </div>
</div>
<script>
    (function () {
        const priority = document.getElementById('priority');
        const category = document.getElementById('category');
        const dueDate = document.getElementById('due_date');
        if (!priority || !category || !dueDate) return;
        const noDueCategories = ['proyecto', 'mejora preventiva', 'automatizacion'];

        const addDays = (days) => {
            const d = new Date();
            d.setDate(d.getDate() + days);
            return d.toISOString().split('T')[0];
        };

        const calculate = () => {
            if (noDueCategories.includes(category.value)) {
                dueDate.value = '';
                return;
            }
            if (priority.value === 'baja') dueDate.value = addDays(5);
            if (priority.value === 'media') dueDate.value = addDays(3);
            if (priority.value === 'alta') dueDate.value = addDays(1);
        };

        priority.addEventListener('change', calculate);
        category.addEventListener('change', calculate);
        calculate();
    })();
</script>
</body>
</html>
