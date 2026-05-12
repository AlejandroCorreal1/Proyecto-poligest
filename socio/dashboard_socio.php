<?php
/**
 * DASHBOARD PARA SOCIOS
 */

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'socio') {
    header('Location: ../login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Cuenta - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-building me-2"></i> Polideportivo
        </a>
        <div>
            <span class="text-white me-3">
                <i class="fas fa-user me-1"></i> <?= $_SESSION['nombre'] . ' ' . $_SESSION['apellidos'] ?>
                <span class="badge bg-secondary ms-1">Socio</span>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i> Salir
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    
    <div class="row mt-4">
        
        <!-- Tarjeta 1: Ver mis reservas -->
        <div class="col-md-6 mb-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-calendar-alt fa-3x text-primary mb-2"></i>
                    <h5>Mis Reservas</h5>
                    <p class="text-muted small">Consultar reservas actuales</p>
                    <a href="mis_reservas.php" class="btn btn-primary btn-sm">Ver</a>
                </div>
            </div>
        </div>
        
        <!-- Tarjeta 2: Nueva reserva -->
        <div class="col-md-6 mb-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="fas fa-plus-circle fa-3x text-success mb-2"></i>
                    <h5>Nueva Reserva</h5>
                    <p class="text-muted small">Reservar una instalación</p>
                    <a href="nueva_reserva.php" class="btn btn-success btn-sm">Reservar</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Datos personales -->
    <div class="card mt-3">
        <div class="card-header bg-info text-white">
            <i class="fas fa-id-card me-2"></i> Mis Datos Personales
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>DNI:</strong> <?= $_SESSION['socio_datos']['dni'] ?></div>
                <div class="col-md-3"><strong>Nombre:</strong> <?= $_SESSION['socio_datos']['nombre'] ?></div>
                <div class="col-md-3"><strong>Teléfono:</strong> <?= $_SESSION['socio_datos']['telefono'] ?></div>
                <div class="col-md-3"><strong>Cuota:</strong> <?= $_SESSION['socio_datos']['tipo_cuota'] ?></div>
            </div>
        </div>
    </div>
    
    <!-- Deportes disponibles -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <i class="fas fa-futbol me-2"></i> Instalaciones disponibles
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-primary p-2">⚽ Fútbol</span>
                <span class="badge bg-primary p-2">🏀 Baloncesto</span>
                <span class="badge bg-primary p-2">🎾 Tenis</span>
                <span class="badge bg-primary p-2">🏓 Pádel</span>
                <span class="badge bg-primary p-2">🏊 Natación</span>
            </div>
        </div>
    </div>
    
</div>

</body>
</html>