<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

// Estadísticas
$totalInstalaciones = $pdo->query("SELECT COUNT(*) as total FROM instalaciones")->fetch();
$totalSocios = $pdo->query("SELECT COUNT(*) as total FROM socios WHERE activo = 1")->fetch();
$totalReservasHoy = $pdo->query("SELECT COUNT(*) as total FROM reservas WHERE fecha = CURDATE()")->fetch();
$totalEmpleados = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1")->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informes - Polideportivo</title>
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
    <h2><i class="fas fa-chart-line me-2"></i> Informes del Polideportivo</h2>
    
    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <i class="fas fa-building fa-3x mb-2"></i>
                    <h2><?= $totalInstalaciones['total'] ?></h2>
                    <h6>Instalaciones</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <i class="fas fa-users fa-3x mb-2"></i>
                    <h2><?= $totalSocios['total'] ?></h2>
                    <h6>Socios Activos</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center bg-info text-white">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-3x mb-2"></i>
                    <h2><?= $totalReservasHoy['total'] ?></h2>
                    <h6>Reservas Hoy</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center bg-warning text-white">
                <div class="card-body">
                    <i class="fas fa-user-tie fa-3x mb-2"></i>
                    <h2><?= $totalEmpleados['total'] ?></h2>
                    <h6>Empleados</h6>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>