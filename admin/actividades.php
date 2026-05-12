<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';

$actividades = $pdo->query("SELECT a.*, i.nombre as instalacion_nombre FROM actividades a LEFT JOIN instalaciones i ON a.instalacion_id = i.id ORDER BY a.id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Actividades - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../dashboard.php">
            <i class="fas fa-arrow-left me-2"></i> Volver al Panel
        </a>
        <span class="text-white">Administrador: <?= $_SESSION['nombre'] ?></span>
    </div>
</nav>

<div class="container mt-4">
    <h2><i class="fas fa-calendar-alt me-2"></i> Gestionar Actividades</h2>
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Lista de Actividades</h5>
        </div>
        <div class="card-body">
            <a href="nueva_actividad.php" class="btn btn-success mb-3">
                <i class="fas fa-plus me-2"></i> Nueva Actividad
            </a>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Nombre</th><th>Instalación</th><th>Día</th><th>Hora</th><th>Plazas</th><th>Estado</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($actividades as $act): ?>
                        <tr>
                            <td><?= $act['id'] ?></td>
                            <td><?= htmlspecialchars($act['nombre']) ?></td>
                            <td><?= htmlspecialchars($act['instalacion_nombre']) ?></td>
                            <td><?= ucfirst($act['dia_semana']) ?></td>
                            <td><?= substr($act['hora_inicio'], 0, 5) ?> - <?= substr($act['hora_fin'], 0, 5) ?></td>
                            <td><?= $act['plazas'] ?></td>
                            <td><?= $act['activa'] ? '✅ Activa' : '❌ Inactiva' ?></td>
                            <td>
                                <a href="editar_actividad.php?id=<?= $act['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="?eliminar=<?= $act['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>