<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones - Tasking</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 24px; color: #1f2937; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: #fff; border-radius: 10px; padding: 18px; margin-bottom: 12px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
        .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; gap: 10px; flex-wrap: wrap; }
        .btn { background: #1d4ed8; color: #fff; text-decoration: none; padding: 10px 14px; border-radius: 8px; display: inline-block; }
        .tag { font-size: 12px; padding: 3px 8px; border-radius: 999px; background: #fee2e2; color: #991b1b; }
        .read { background: #dcfce7; color: #166534; }
        .muted { color: #6b7280; font-size: 13px; }
        button { border: none; padding: 8px 12px; border-radius: 8px; background: #0f766e; color: #fff; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <div class="top">
        <h1>Notificaciones</h1>
        <a class="btn" href="{{ route('tasks.index') }}">Volver a tareas</a>
    </div>

    @forelse($notifications as $item)
        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
                <strong>{{ $item->title }}</strong>
                @if($item->read_at)
                    <span class="tag read">Leida</span>
                @else
                    <span class="tag">No leida</span>
                @endif
            </div>
            <p>{{ $item->message }}</p>
            <p class="muted">Creada: {{ $item->created_at }}</p>
            @if(!$item->read_at)
                <form method="POST" action="{{ route('notifications.read', $item->id) }}">
                    @csrf
                    <button type="submit">Marcar como leida</button>
                </form>
            @endif
        </div>
    @empty
        <div class="card">
            <p>No tienes notificaciones.</p>
        </div>
    @endforelse
</div>
</body>
</html>
