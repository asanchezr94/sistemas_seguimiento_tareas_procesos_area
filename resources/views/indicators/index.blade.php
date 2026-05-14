<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 24px; color: #1f2937; }
        .container { max-width: 1200px; margin: 0 auto; }
        .brand-head { display: flex; align-items: flex-end; gap: 12px; margin: 0 0 6px; flex-wrap: wrap; }
        .brand-logo { max-height: 96px; width: auto; display: block; }
        .brand-title { font-size: 28px; font-weight: 700; color: #000; padding-bottom: 6px; }
        .card { background: #fff; border-radius: 10px; padding: 18px; box-shadow: 0 4px 16px rgba(0,0,0,.06); margin-bottom: 14px; }
        .btn { display:inline-block; background:#0f766e; color:#fff; padding:10px 14px; border-radius:8px; text-decoration:none; font-weight:700; border:none; cursor:pointer; }
        .grid { display:grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap:10px; }
        .kpi { background:#f9fafb; border-radius:8px; padding:12px; border:1px solid #e5e7eb; }
        .kpi .n { font-size:28px; font-weight:700; margin-top:6px; }
        .muted { color:#6b7280; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; text-align: left; padding: 10px 8px; }
        th { background:#f9fafb; }
        input[type="month"] { border:1px solid #d1d5db; border-radius:8px; padding:10px; }
        @media (max-width: 1000px) { .grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <div class="brand-head">
        <img class="brand-logo" src="{{ asset('images/logo-cotrasena.png') }}" alt="COTRASENA">
        <div class="brand-title">- Gestor de tareas por área</div>
    </div>
    <h1>Indicadores</h1>
    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin:10px 0 18px;flex-wrap:wrap;">
        <div><strong>Usuario:</strong> {{ auth()->user()->name }} ({{ auth()->user()->role }})</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a class="btn" href="{{ route('tasks.index') }}">Inicio</a>
            <a class="btn" href="{{ route('indicators.index') }}">Indicadores</a>
            <a class="btn" style="background:#ef4444;" href="{{ route('notifications.index') }}">Notificaciones ({{ $unreadNotificationsCount }})</a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button class="btn" type="submit">Cerrar sesion</button>
            </form>
        </div>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('indicators.index') }}" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap;">
            <div>
                <label for="month" style="display:block;font-weight:700;margin-bottom:6px;">Filtrar por mes</label>
                <input id="month" type="month" name="month" value="{{ $monthFilter }}">
            </div>
            <div>
                <button class="btn" type="submit">Aplicar</button>
            </div>
            <div class="muted">Mes seleccionado: {{ ucfirst($monthLabel) }}</div>
        </form>
    </div>

    <div class="card">
        <h2>Resumen del Mes</h2>
        <div class="grid">
            <div class="kpi"><div>Tareas registradas</div><div class="n">{{ $totalMonth }}</div></div>
            <div class="kpi"><div>Pendientes</div><div class="n">{{ $statusCountsMonth['pendiente'] }}</div></div>
            <div class="kpi"><div>En proceso</div><div class="n">{{ $statusCountsMonth['en proceso'] }}</div></div>
            <div class="kpi"><div>Escalamiento</div><div class="n">{{ $statusCountsMonth['escalamiento'] }}</div></div>
            <div class="kpi"><div>Terminadas</div><div class="n">{{ $statusCountsMonth['terminado'] }}</div></div>
        </div>
    </div>

    <div class="card">
        <h2>Cumplimiento de Tiempo (SLA)</h2>
        <div class="grid">
            <div class="kpi"><div>Mes: Resueltas a tiempo</div><div class="n">{{ $slaOnTimeMonth }}</div></div>
            <div class="kpi"><div>Mes: Resueltas fuera de tiempo</div><div class="n">{{ $slaLateMonth }}</div></div>
            <div class="kpi"><div>Mes: Total evaluadas</div><div class="n">{{ $slaTotalMonth }}</div></div>
            <div class="kpi"><div>Mes: % cumplimiento</div><div class="n">{{ $slaRateMonth }}%</div></div>
            <div class="kpi"><div>Acumulado: % cumplimiento</div><div class="n">{{ $slaRateAll }}%</div></div>
        </div>
        <table style="margin-top:12px;">
            <thead>
                <tr><th>Indicador acumulado</th><th>Valor</th></tr>
            </thead>
            <tbody>
                <tr><td>Resueltas a tiempo</td><td>{{ $slaOnTimeAll }}</td></tr>
                <tr><td>Resueltas fuera de tiempo</td><td>{{ $slaLateAll }}</td></tr>
                <tr><td>Total evaluadas</td><td>{{ $slaTotalAll }}</td></tr>
            </tbody>
        </table>
        <p class="muted" style="margin-top:8px;">Se evalúan solo tareas terminadas con fecha de vencimiento y fecha de terminación.</p>
    </div>

    <div class="card">
        <h2>Categorias (Mes y Acumulado)</h2>
        <div class="kpi" style="margin-bottom:10px;"><div>Total tareas historicas</div><div class="n">{{ $totalAll }}</div></div>
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Mes: Creadas</th>
                    <th>Mes: Terminadas</th>
                    <th>Mes: Min. terminadas</th>
                    <th>Acumulado: Creadas</th>
                    <th>Acumulado: Terminadas</th>
                    <th>Acumulado: Min. terminadas</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Proyecto</td><td>{{ $categoryStatsMonth['proyecto']['creadas'] }}</td><td>{{ $categoryStatsMonth['proyecto']['terminadas'] }}</td><td>{{ $categoryStatsMonth['proyecto']['minutos_terminadas'] }}</td><td>{{ $categoryStatsAll['proyecto']['creadas'] }}</td><td>{{ $categoryStatsAll['proyecto']['terminadas'] }}</td><td>{{ $categoryStatsAll['proyecto']['minutos_terminadas'] }}</td></tr>
                <tr><td>Automatizacion</td><td>{{ $categoryStatsMonth['automatizacion']['creadas'] }}</td><td>{{ $categoryStatsMonth['automatizacion']['terminadas'] }}</td><td>{{ $categoryStatsMonth['automatizacion']['minutos_terminadas'] }}</td><td>{{ $categoryStatsAll['automatizacion']['creadas'] }}</td><td>{{ $categoryStatsAll['automatizacion']['terminadas'] }}</td><td>{{ $categoryStatsAll['automatizacion']['minutos_terminadas'] }}</td></tr>
                <tr><td>Mejora preventiva</td><td>{{ $categoryStatsMonth['mejora preventiva']['creadas'] }}</td><td>{{ $categoryStatsMonth['mejora preventiva']['terminadas'] }}</td><td>{{ $categoryStatsMonth['mejora preventiva']['minutos_terminadas'] }}</td><td>{{ $categoryStatsAll['mejora preventiva']['creadas'] }}</td><td>{{ $categoryStatsAll['mejora preventiva']['terminadas'] }}</td><td>{{ $categoryStatsAll['mejora preventiva']['minutos_terminadas'] }}</td></tr>
                <tr><td>Soporte</td><td>{{ $categoryStatsMonth['soporte']['creadas'] }}</td><td>{{ $categoryStatsMonth['soporte']['terminadas'] }}</td><td>{{ $categoryStatsMonth['soporte']['minutos_terminadas'] }}</td><td>{{ $categoryStatsAll['soporte']['creadas'] }}</td><td>{{ $categoryStatsAll['soporte']['terminadas'] }}</td><td>{{ $categoryStatsAll['soporte']['minutos_terminadas'] }}</td></tr>
                <tr><td>Fallas masivas</td><td>{{ $operationalStatsMonth['fallas masivas']['creadas'] }}</td><td>{{ $operationalStatsMonth['fallas masivas']['terminadas'] }}</td><td>{{ $operationalStatsMonth['fallas masivas']['minutos_terminadas'] }}</td><td>{{ $operationalStatsAll['fallas masivas']['creadas'] }}</td><td>{{ $operationalStatsAll['fallas masivas']['terminadas'] }}</td><td>{{ $operationalStatsAll['fallas masivas']['minutos_terminadas'] }}</td></tr>
                <tr><td>OPA</td><td>{{ $operationalStatsMonth['opa']['creadas'] }}</td><td>{{ $operationalStatsMonth['opa']['terminadas'] }}</td><td>{{ $operationalStatsMonth['opa']['minutos_terminadas'] }}</td><td>{{ $operationalStatsAll['opa']['creadas'] }}</td><td>{{ $operationalStatsAll['opa']['terminadas'] }}</td><td>{{ $operationalStatsAll['opa']['minutos_terminadas'] }}</td></tr>
                <tr><td>Visionamos</td><td>{{ $operationalStatsMonth['visionamos']['creadas'] }}</td><td>{{ $operationalStatsMonth['visionamos']['terminadas'] }}</td><td>{{ $operationalStatsMonth['visionamos']['minutos_terminadas'] }}</td><td>{{ $operationalStatsAll['visionamos']['creadas'] }}</td><td>{{ $operationalStatsAll['visionamos']['terminadas'] }}</td><td>{{ $operationalStatsAll['visionamos']['minutos_terminadas'] }}</td></tr>
                <tr><td>App movil</td><td>{{ $operationalStatsMonth['app movil']['creadas'] }}</td><td>{{ $operationalStatsMonth['app movil']['terminadas'] }}</td><td>{{ $operationalStatsMonth['app movil']['minutos_terminadas'] }}</td><td>{{ $operationalStatsAll['app movil']['creadas'] }}</td><td>{{ $operationalStatsAll['app movil']['terminadas'] }}</td><td>{{ $operationalStatsAll['app movil']['minutos_terminadas'] }}</td></tr>
                <tr><td>Configuraciones</td><td>{{ $operationalStatsMonth['configuraciones']['creadas'] }}</td><td>{{ $operationalStatsMonth['configuraciones']['terminadas'] }}</td><td>{{ $operationalStatsMonth['configuraciones']['minutos_terminadas'] }}</td><td>{{ $operationalStatsAll['configuraciones']['creadas'] }}</td><td>{{ $operationalStatsAll['configuraciones']['terminadas'] }}</td><td>{{ $operationalStatsAll['configuraciones']['minutos_terminadas'] }}</td></tr>
                <tr><td>Proveedores</td><td>{{ $operationalStatsMonth['proveedores']['creadas'] }}</td><td>{{ $operationalStatsMonth['proveedores']['terminadas'] }}</td><td>{{ $operationalStatsMonth['proveedores']['minutos_terminadas'] }}</td><td>{{ $operationalStatsAll['proveedores']['creadas'] }}</td><td>{{ $operationalStatsAll['proveedores']['terminadas'] }}</td><td>{{ $operationalStatsAll['proveedores']['minutos_terminadas'] }}</td></tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
