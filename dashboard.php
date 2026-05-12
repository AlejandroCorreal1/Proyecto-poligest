<?php
/**
 * DASHBOARD - Panel de control para empleados
 * Roles: administrador, recepcionista, monitor
 */

session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if($_SESSION['rol'] == 'socio') {
    header('Location: socio/dashboard_socio.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .card:hover { transform: translateY(-5px); transition: 0.3s; }
        .card { transition: 0.3s; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-building me-2"></i> Polideportivo
        </a>
        <div>
            <span class="text-white me-3">
                <i class="fas fa-user me-1"></i> 
                <?= $_SESSION['nombre'] . ' ' . $_SESSION['apellidos'] ?>
                <span class="badge bg-secondary ms-1"><?= $_SESSION['rol'] ?></span>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i> Salir
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    
    <!-- ========================================================= -->
    <!-- PANEL ADMINISTRADOR                                       -->
    <!-- ========================================================= -->
    <?php if($_SESSION['rol'] == 'administrador'): ?>
        <div class="row mt-4">
            
            <!-- Tarjeta 1: Alta de empleados -->
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-user-plus fa-3x text-primary mb-2"></i>
                        <h5>Alta Empleados</h5>
                        <p class="text-muted small">Crear recepcionistas y monitores</p>
                        <a href="admin/alta_empleado.php" class="btn btn-primary btn-sm">Ir</a>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta 2: Gestionar instalaciones -->
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-building fa-3x text-success mb-2"></i>
                        <h5>Instalaciones</h5>
                        <p class="text-muted small">Gestionar pistas y precios</p>
                        <a href="admin/instalaciones.php" class="btn btn-success btn-sm">Ir</a>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta 3: Gestionar actividades -->
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt fa-3x text-warning mb-2"></i>
                        <h5>Actividades</h5>
                        <p class="text-muted small">Clases y horarios</p>
                        <a href="admin/actividades.php" class="btn btn-warning btn-sm">Ir</a>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta 4: Informes -->
            <div class="col-md-3 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-3x text-info mb-2"></i>
                        <h5>Informes</h5>
                        <p class="text-muted small">Estadísticas del centro</p>
                        <a href="admin/informes.php" class="btn btn-info btn-sm">Ir</a>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- ========================================================= -->
    <!-- PANEL RECEPCIONISTA                                       -->
    <!-- ========================================================= -->
    <?php elseif($_SESSION['rol'] == 'recepcionista'): ?>
        <div class="row mt-4">
            
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-calendar-check fa-3x text-primary mb-2"></i>
                        <h5>Reservas</h5>
                        <p class="text-muted small">Gestionar reservas</p>
                        <a href="recepcion/reservas.php" class="btn btn-primary btn-sm">Ir</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x text-success mb-2"></i>
                        <h5>Socios</h5>
                        <p class="text-muted small">Dar de alta socios</p>
                        <a href="recepcion/socios.php" class="btn btn-success btn-sm">Ir</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-search fa-3x text-info mb-2"></i>
                        <h5>Buscar</h5>
                        <p class="text-muted small">Buscar socios</p>
                        <a href="recepcion/buscar.php" class="btn btn-info btn-sm">Ir</a>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- ========================================================= -->
    <!-- PANEL MONITOR                                            -->
    <!-- ========================================================= -->
    <?php elseif($_SESSION['rol'] == 'monitor'): ?>
        <div class="row mt-4">
            
            <div class="col-md-6 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-clock fa-3x text-primary mb-2"></i>
                        <h5>Mis Horarios</h5>
                        <p class="text-muted small">Clases que impartes</p>
                        <a href="monitor/horarios.php" class="btn btn-primary btn-sm">Ir</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card text-center h-100 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-list-check fa-3x text-success mb-2"></i>
                        <h5>Asistentes</h5>
                        <p class="text-muted small">Participantes en tus clases</p>
                        <a href="monitor/asistentes.php" class="btn btn-success btn-sm">Ir</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
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