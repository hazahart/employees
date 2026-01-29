<?php
require_once 'db.php';

$pdo = getDB();

$stmtDept = $pdo->query("SELECT dept_name FROM departments ORDER BY dept_name");
$departamentos = $stmtDept->fetchAll(PDO::FETCH_COLUMN);

$reporte2 = $pdo->query("SELECT * FROM v_managers")->fetchAll();
$reporte3 = $pdo->query("SELECT * FROM v_mejor_pagado")->fetchAll();
$reporte4 = $pdo->query("SELECT * FROM v_contrataciones")->fetchAll();
$reporte5 = $pdo->query("SELECT * FROM v_stats_dept")->fetchAll();

$graf1_data = $pdo->query("SELECT * FROM v_graf_genero")->fetchAll();
$jsonGraf1_labels = json_encode(array_column($graf1_data, 'gender'));
$jsonGraf1_data = json_encode(array_column($graf1_data, 'total'));

$graf2_data = $pdo->query("SELECT * FROM v_graf_top_10")->fetchAll();
$jsonGraf2_labels = json_encode(array_column($graf2_data, 'nombre'));
$jsonGraf2_data = json_encode(array_column($graf2_data, 'salary'));

$jsonDepts = json_encode(array_column($reporte5, 'dept_name'));
$jsonPromedios = json_encode(array_column($reporte5, 'salario_promedio'));
$jsonBrechas = json_encode(array_column($reporte5, 'brecha_salarial'));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen Diagnóstico Big Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" integrity="sha384-q6bAgUAsga3oT16XWJ1toXdKcHmBp45jM5roe3RCQ6dET9xGL89Qmpx4tJAI2pm2" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">

        <div class="cupertino-card text-center">
            <h1 class="display-3">Examen Diagnóstico: Big Data</h1>
            <h2 class="display-5">Prof. José Jesús Sánchez Farías</h2>
            <h3 class="display-6">Equipo:</h3>
            <p class="lead" style="margin: 0px;">Arreola García Vanessa Fernanda</p>
            <p class="lead" style="margin: 0px;">Ramírez Mireles Gustavo</p>
            <p class="lead" style="margin: 0px;">Silva Méndez Abraham</p>
        </div>

        <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill"
                    data-bs-target="#reportes">Reportes</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill"
                    data-bs-target="#garficos">Gráficos</button></li>
        </ul>

        <div class="tab-content" id="pills-tabContent">

            <div class="tab-pane fade show active" id="reportes">

                <div class="cupertino-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3>Lista de todos los empleados</h3>
                        </div>
                        <div class="d-flex">
                            <select id="filtroDepartamento" class="form-select me-2">
                                <option value="">Todos los Departamentos</option>
                                <?php foreach ($departamentos as $dept): ?>
                                    <option value="<?= $dept ?>"><?= $dept ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tablaEmpleados" class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nombre</th>
                                    <th>Género</th>
                                    <th>Fecha Nac.</th>
                                    <th>Contratación</th>
                                    <th>Departamento</th>
                                    <th>Título</th>
                                    <th>Salario</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="cupertino-card">
                            <h3>Managers Actuales</h3>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Departamento</th>
                                        <th>Manager</th>
                                        <th>Desde</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reporte2 as $row): ?>
                                        <tr>
                                            <td><?= $row['dept_name'] ?></td>
                                            <td><?= $row['nombre_manager'] ?></td>
                                            <td><?= $row['fecha_inicio'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cupertino-card">
                            <h3>Mejor Pagado por Departamento</h3>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Departamento</th>
                                        <th>Empleado</th>
                                        <th>Salario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reporte3 as $row): ?>
                                        <tr>
                                            <td><?= $row['dept_name'] ?></td>
                                            <td><?= $row['nombre_completo'] ?></td>
                                            <td class="text-success fw-bold">$<?= number_format($row['salary']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="cupertino-card">
                            <h3>Contrataciones por Año</h3>
                            <table id="tablaContrataciones" class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Año</th>
                                        <th>Contrataciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reporte4 as $row): ?>
                                        <tr>
                                            <td><?= $row['anio'] ?></td>
                                            <td><?= $row['total_contratados'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="cupertino-card">
                            <h3>5. Estadísticas Generales</h3>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Departamento</th>
                                        <th>Total Empleados</th>
                                        <th>Salario Promedio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reporte5 as $row): ?>
                                        <tr>
                                            <td><?= $row['dept_name'] ?></td>
                                            <td><?= number_format($row['total_empleados']) ?></td>
                                            <td>$<?= number_format($row['salario_promedio'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="garficos">
                <div class="row">
                    <div class="col-md-6">
                        <div class="cupertino-card">
                            <h4 class="text-center">Hombres vs Mujeres</h4>
                            <div style="height:300px"><canvas id="graficoHM"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cupertino-card">
                            <h4 class="text-center">Top 10 Salarios</h4>
                            <div style="height:300px"><canvas id="graficoTop10"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="cupertino-card">
                            <h4 class="text-center">Promedio Salarial por Departamento</h4>
                            <div style="height:300px"><canvas id="graficoPromedio"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cupertino-card">
                            <h4 class="text-center">Brecha Salarial</h4>
                            <div style="height:300px"><canvas id="graficoBrecha"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            $(document).ready(function () {
                var table = $('#tablaEmpleados').DataTable({
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: 'data_employees.php',
                        type: 'GET',
                        data: function (d) {
                            d.dept_filter = $('#filtroDepartamento').val();
                        }
                    },
                    order: [[0, 'asc']],
                    columns: [
                        { orderable: true },
                        { orderable: true },
                        { orderable: true },
                        { orderable: true },
                        { orderable: true },
                        { orderable: true },
                        { orderable: true },
                        { orderable: true }
                    ]
                });

                $('#filtroDepartamento').on('change', function () {
                    table.draw();
                });

                $('#tablaContrataciones').DataTable({
                    language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json' },
                    searching: false,
                    lengthChange: false,
                    pageLength: 10,
                    order: [[0, 'desc']]
                });
            });

            Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto';
            Chart.defaults.color = '#333';
            Chart.defaults.maintainAspectRatio = false;

            new Chart(document.getElementById('graficoHM'), {
                type: 'pie',
                data: {
                    labels: <?= $jsonGraf1_labels ?>,
                    datasets: [{
                        data: <?= $jsonGraf1_data ?>,
                        backgroundColor: ['#007aff', '#ff2d55'],
                        borderWidth: 0
                    }]
                }
            });

            new Chart(document.getElementById('graficoTop10'), {
                type: 'bar',
                data: {
                    labels: <?= $jsonGraf2_labels ?>,
                    datasets: [{
                        label: 'Salario',
                        data: <?= $jsonGraf2_data ?>,
                        backgroundColor: 'rgba(52, 199, 89, 0.7)',
                        borderColor: '#34c759',
                        borderWidth: 1
                    }]
                },
                options: { indexAxis: 'y' }
            });

            new Chart(document.getElementById('graficoPromedio'), {
                type: 'bar',
                data: {
                    labels: <?= $jsonDepts ?>,
                    datasets: [{
                        label: 'Salario Promedio',
                        data: <?= $jsonPromedios ?>,
                        backgroundColor: 'rgba(88, 86, 214, 0.7)',
                        borderRadius: 5
                    }]
                }
            });

            new Chart(document.getElementById('graficoBrecha'), {
                type: 'line',
                data: {
                    labels: <?= $jsonDepts ?>,
                    datasets: [{
                        label: 'Diferencia',
                        data: <?= $jsonBrechas ?>,
                        borderColor: '#ff9500',
                        backgroundColor: 'rgba(255, 149, 0, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                }
            });
        </script>
</body>

</html>