<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas de Dispositivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card { border-radius:12px; transition:all .3s; background:#fff; }
        .stat-card:hover { transform:translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,.08); }
        .card-body h2 { font-weight:bold; font-size:2rem; }
        .card-body p  { font-size:.9rem; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">

    <!-- Título y botones -->
    <div class="mb-5">
        <h2 class="text-primary fw-bold">📊 Estadísticas de Dispositivos</h2>
        <div class="d-flex justify-content-end gap-2 mb-3">
            <a href="index.php?c=prestamo&a=dashboard" class="btn btn-outline-secondary">← Volver</a>
            <button id="toggle-dark-mode" class="btn btn-outline-secondary">🌓 Modo oscuro</button>
        </div>
        <div class="mb-4 text-end">
            <form method="GET" action="index.php" style="display:inline;">
                <input type="hidden" name="c" value="exportar">
                <input type="hidden" name="a" value="pdf">
                <input type="hidden" name="tipo" value="<?= htmlspecialchars($_GET['tipo'] ?? '') ?>">
                <input type="hidden" name="marca" value="<?= htmlspecialchars($_GET['marca'] ?? '') ?>">
                <input type="hidden" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
                <input type="hidden" name="fecha_fin" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-danger me-2">📄 Exportar PDF</button>
            </form>
            <form method="GET" action="index.php" style="display:inline;">
                <input type="hidden" name="c" value="exportar">
                <input type="hidden" name="a" value="excel">
                <input type="hidden" name="tipo" value="<?= htmlspecialchars($_GET['tipo'] ?? '') ?>">
                <input type="hidden" name="marca" value="<?= htmlspecialchars($_GET['marca'] ?? '') ?>">
                <input type="hidden" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
                <input type="hidden" name="fecha_fin" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
                <button type="submit" class="btn btn-outline-success">📊 Exportar Excel</button>
            </form>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="index.php" class="row g-3 mb-5">
        <input type="hidden" name="c" value="dispositivo">
        <input type="hidden" name="a" value="stats">

        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select id="filtro-tipo" name="tipo" class="form-select">
                <option value="">-- Todos los tipos --</option>
                <?php foreach ($tiposDisponibles as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>"
                        <?= (isset($_GET['tipo']) && $_GET['tipo']===$t)?'selected':'' ?>>
                        <?= ucfirst(htmlspecialchars($t)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Marca</label>
            <select id="filtro-marca" name="marca" class="form-select">
                <option value="">-- Todas las marcas --</option>
                <?php foreach ($marcasDisponibles as $m): ?>
                    <option value="<?= htmlspecialchars($m) ?>"
                        <?= (isset($_GET['marca']) && $_GET['marca']===$m)?'selected':'' ?>>
                        <?= htmlspecialchars($m) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Desde</label>
            <input type="date" name="fecha_inicio" class="form-control"
                   value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label">Hasta</label>
            <input type="date" name="fecha_fin" class="form-control"
                   value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
        </div>

        <div class="col-md-2 d-grid align-self-end">
            <button type="submit" class="btn btn-primary">🔎 Filtrar</button>
            <a href="index.php?c=dispositivo&a=stats"
               class="btn btn-outline-secondary mt-2">🔄 Reiniciar filtros</a>
        </div>
    </form>

    <!-- Tarjetas -->
    <div class="row row-cols-1 row-cols-md-5 g-4 mb-5">
        <?php
        $cols = [
            ['valor'=> $stats['total'] ?? 0,      'texto'=>'Total',       'clase'=>'text-primary'],
            ['valor'=> $stats['activos'] ?? 0,    'texto'=>'Activos',     'clase'=>'text-success'],
            ['valor'=> $stats['bajas'] ?? 0,      'texto'=>'Dados de baja','clase'=>'text-danger'],
            ['valor'=> $stats['prestados'] ?? 0,  'texto'=>'Prestados',   'clase'=>'text-warning'],
            ['valor'=> $stats['sin_asignar'] ?? 0,'texto'=>'Sin asignar', 'clase'=>'text-secondary'],
        ];
        foreach ($cols as $c): ?>
            <div class="col">
                <div class="card h-100 text-center border-0 shadow-sm stat-card">
                    <div class="card-body">
                        <h2 class="<?= $c['clase'] ?> mb-2"><?= $c['valor'] ?></h2>
                        <p class="text-muted mb-0 small"><?= $c['texto'] ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Gráficos -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h5 class="text-center mb-4">Distribución por Estado</h5>
                <canvas id="graficoTorta"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h5 class="text-center mb-4">Movimientos recientes</h5>
                <canvas id="graficoBarras"></canvas>
            </div>
        </div>
    </div>

    <!-- Apilado -->
    <div class="row g-4 mt-5">
        <div class="col-12">
            <div class="card shadow-sm p-4">
                <h5 class="text-center mb-4">Estado de Activos vs Prestados por Tipo</h5>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form id="formFiltroTipo" class="d-flex">
                            <select id="filtroTipoApilado" class="form-select me-2">
                                <option value="">-- Todos los tipos --</option>
                                <?php foreach ($tiposDisponibles as $t): ?>
                                    <option value="<?= htmlspecialchars($t) ?>"><?= ucfirst(htmlspecialchars($t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">🔎 Aplicar filtro</button>
                            <button type="button" id="resetFiltro" class="btn btn-outline-secondary ms-2">🔄 Resetear filtro</button>
                        </form>
                    </div>
                </div>
                <canvas id="graficoApilado"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Torta
    const ctxTorta = document.getElementById('graficoTorta').getContext('2d');
    new Chart(ctxTorta, {
        type: 'doughnut',
        data: {
            labels: ['Activos','Dados de Baja','Prestados','Sin asignar'],
            datasets: [{
                data: [
                    <?= $stats['activos'] ?? 0 ?>,
                    <?= $stats['bajas']   ?? 0 ?>,
                    <?= $stats['prestados']?? 0 ?>,
                    <?= $stats['sin_asignar']??0 ?>
                ],
                backgroundColor:['#198754','#dc3545','#ffc107','#6c757d'],
                borderColor:'#fff', borderWidth:2, hoverOffset:10
            }]
        },
        options:{ responsive:true, plugins:{ legend:{ position:'bottom', labels:{ padding:20, boxWidth:15 } } }, animation:{ animateRotate:true, animateScale:true } }
    });

    // Barras
    const labelsMeses = [
        <?php foreach($movimientosMensuales as $m): ?>
            "<?= $m['anio'] . '-' . str_pad($m['mes'],2,'0',STR_PAD_LEFT) ?>",
        <?php endforeach; ?>
    ];
    const datosAltas = [
        <?php foreach($movimientosMensuales as $m): ?>
            <?= $m['altas'] ?>,
        <?php endforeach; ?>
    ];
    const ctxBarras = document.getElementById('graficoBarras').getContext('2d');
    new Chart(ctxBarras,{
        type:'bar',
        data:{ labels:labelsMeses, datasets:[{ label:'Altas', data:datosAltas, backgroundColor:'#0d6efd' }] },
        options:{ responsive:true, plugins:{ legend:{ position:'bottom'} }, scales:{ x:{ grid:{display:false} }, y:{ beginAtZero:true, grid:{color:'#eee'} } }, animation:{ duration:1000, easing:'easeOutBounce' } }
    });

    // Apilado
    const tipos = [ <?php foreach($estadoPorTipo as $e):;?>"<?= htmlspecialchars($e['tipo']) ?>",<?php endforeach;?> ];
    const disponibles = [ <?php foreach($estadoPorTipo as $e):;?><?= $e['disponibles'] ?>,<?php endforeach;?> ];
    const prestados    = [ <?php foreach($estadoPorTipo as $e):;?><?= $e['prestados'] ?>,<?php endforeach;?> ];
    const ctxApilado   = document.getElementById('graficoApilado').getContext('2d');
    const graficoApilado = new Chart(ctxApilado,{
        type:'bar',
        data:{ labels:tipos, datasets:[
            { label:'Disponibles', data:disponibles, backgroundColor:'#198754' },
            { label:'Prestados',   data:prestados,   backgroundColor:'#ffc107' }
        ]},
        options:{ responsive:true, plugins:{ legend:{ position:'bottom' } }, scales:{ x:{ stacked:true }, y:{ stacked:true, beginAtZero:true } } }
    });

    // Filtro dinámico de marcas en stats.php
    document.getElementById('filtro-tipo').addEventListener('change', function() {
        const tipo = this.value;
        const marcaSelect = document.getElementById('filtro-marca');
        marcaSelect.innerHTML = '<option value="">-- Todas las marcas --</option>';
        if (!tipo) return;
        fetch(`index.php?c=dispositivo&a=marcasAjax&tipo=${encodeURIComponent(tipo)}`)
            .then(r => r.json())
            .then(list => list.forEach(m => {
                const o = new Option(m, m);
                marcaSelect.add(o);
            }));
    });

    // Filtro interno apilado
    document.getElementById('formFiltroTipo').addEventListener('submit', e=>{
        e.preventDefault();
        const sel = document.getElementById('filtroTipoApilado').value;
        let labs = [...tipos], dis = [...disponibles], pres = [...prestados];
        if (sel) {
            const i = tipos.indexOf(sel);
            labs = [tipos[i]]; dis = [disponibles[i]]; pres = [prestados[i]];
        }
        graficoApilado.data.labels = labs;
        graficoApilado.data.datasets[0].data = dis;
        graficoApilado.data.datasets[1].data = pres;
        graficoApilado.update();
    });
    document.getElementById('resetFiltro').addEventListener('click', ()=>{
        document.getElementById('filtroTipoApilado').value = '';
        graficoApilado.data.labels = tipos;
        graficoApilado.data.datasets[0].data = disponibles;
        graficoApilado.data.datasets[1].data = prestados;
        graficoApilado.update();
    });
</script>

<script src="public/js/tema.js"></script>
</body>
</html>
