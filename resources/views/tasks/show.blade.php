<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarea #{{ $task->id }} - Tasking</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 24px; color: #1f2937; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: #fff; border-radius: 10px; padding: 18px; margin-bottom: 14px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
        .grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(0,1fr)); }
        .full { grid-column: span 2; }
        label { display: block; margin-bottom: 6px; font-weight: 700; font-size: 13px; }
        input, select, textarea, button { width: 100%; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; }
        textarea { min-height: 90px; }
        button { background: #0f766e; color: #fff; border: none; cursor: pointer; font-weight: 700; }
        .muted { color: #6b7280; }
        .ok { background: #dcfce7; color: #166534; padding: 8px 10px; border-radius: 8px; margin-bottom: 10px; }
        .err { background: #fee2e2; color: #991b1b; padding: 8px 10px; border-radius: 8px; margin-bottom: 10px; }
        @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } .full { grid-column: span 1; } }
    </style>
</head>
<body>
<div class="container">
    <p><a href="{{ route('tasks.index') }}">← Volver a tareas</a></p>
    <div class="card">
        <h1>Tarea #{{ $task->id }}</h1>
        @if(session('success')) <div class="ok">{{ session('success') }}</div> @endif
        @if($errors->any()) <div class="err">{{ $errors->first() }}</div> @endif

        <div class="grid">
            <div><label>Usuario</label><input value="{{ $task->user_name }}" readonly></div>
            <div><label>Creada</label><input value="{{ $task->task_date }}" readonly></div>
            <div><label>Vence</label><input value="{{ $task->due_date }}" readonly></div>
            <div><label>Fecha solucion</label><input value="{{ $task->resolution_date ?? '-' }}" readonly></div>
            <div><label>Minutos invertidos</label><input value="{{ $task->minutes_spent ?? '-' }}" readonly></div>
            <div class="full"><label>Nombre</label><input value="{{ $task->task_name }}" readonly></div>
            <div class="full"><label>Detalle</label><textarea readonly>{{ $task->task_detail }}</textarea></div>
            <div><label>Categoria</label><input value="{{ $task->category }}" readonly></div>
        </div>
    </div>

    <div class="card">
        <h2>Cambiar estado/prioridad</h2>
        @if($task->status === 'terminado')
            <p class="muted">La tarea esta terminada. No se permiten cambios de datos.</p>
        @else
            <form method="POST" action="{{ route('tasks.update', $task->id) }}">
                @csrf
                @method('PUT')
                <div class="grid">
                    <div>
                        <label for="priority">Prioridad</label>
                        <select id="priority" name="priority" required>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority }}" @selected($task->priority === $priority)>{{ ucfirst($priority) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status">Estado</label>
                        <select id="status" name="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected($task->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="minutes_spent">Minutos invertidos</label>
                        <input id="minutes_spent" name="minutes_spent" type="number" step="1" min="1" value="{{ old('minutes_spent', $task->minutes_spent) }}">
                    </div>
                    <div class="full"><button type="submit">Guardar cambios</button></div>
                </div>
                <p class="muted">No hay opcion de borrar tareas.</p>
            </form>
        @endif
    </div>

    <div class="card">
        <h2>Comentarios</h2>
        <form method="POST" action="{{ route('tasks.comments.store', $task->id) }}">
            @csrf
            <label for="comment">Agregar comentario</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit" style="margin-top:10px;">Guardar comentario</button>
        </form>
        <hr style="margin:16px 0;">
        @forelse($task->comments as $comment)
            <div style="margin-bottom:12px;">
                <strong>{{ $comment->user_name }}</strong>
                <span class="muted">- {{ $comment->created_at }}</span>
                <div>{{ $comment->comment }}</div>
            </div>
        @empty
            <p>No hay comentarios.</p>
        @endforelse
    </div>
</div>
@if($task->status !== 'terminado')
    <script>
        (function () {
            const status = document.getElementById('status');
            const minutes = document.getElementById('minutes_spent');
            if (!status || !minutes) return;

            const syncHoursRequired = () => {
                const isDone = status.value === 'terminado';
                minutes.required = isDone;
                minutes.disabled = !isDone;
                if (!isDone) {
                    minutes.value = '';
                }
            };

            status.addEventListener('change', syncHoursRequired);
            syncHoursRequired();
        })();
    </script>
@endif
</body>
</html>
