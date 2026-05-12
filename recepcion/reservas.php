<?php
/**
 * =============================================================================
 * GESTIONAR RESERVAS - Solo para recepcionistas
 * =============================================================================
 * 
 * FUNCIONALIDAD:
 * - Ver todas las reservas activas (futuras, no canceladas)
 * - Cancelar reservas cuando sea necesario
 * - Acceso a creación de nuevas reservas
 * 
 * PERMISOS:
 * - El recepcionista puede gestionar reservas de CUALQUIER socio
 * - No puede modificar instalaciones ni actividades
 * 
 * OPERACIONES:
 * - SELECT: Muestra listado de reservas con JOIN para obtener nombres
 * - UPDATE: Cambia estado de reserva a 'cancelada'
 * - Enlace a nueva_reserva.php para INSERT
 */

// Iniciar sesión para acceder a datos del usuario logueado
session_start();

// =============================================================================
// CONTROL DE ACCESO: Solo recepcionistas pueden entrar
// =============================================================================
// Si no hay sesión O el rol no es 'recepcionista', redirigir al login
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'recepcionista') {
    header('Location: ../login.php');
    exit();
}

// Incluir conexión a la base de datos
require_once '../config/database.php';

// Variables para mensajes al usuario
$mensaje = '';
$error = '';

// =============================================================================
// PROCESAR CANCELACIÓN DE RESERVA
// =============================================================================
// Si la URL contiene ?cancelar=ID, se cancela esa reserva
if (isset($_GET['cancelar'])) {
    $id_reserva = $_GET['cancelar'];
    
    try {
        // UPDATE: Cambiar estado de la reserva a 'cancelada'
        // Los signos ? son marcadores de posición (seguro contra inyección SQL)
        $consulta = $pdo->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id = ?");
        $consulta->execute([$id_reserva]);
        $mensaje = "Reserva cancelada correctamente";
    } catch(PDOException $e) {
        $error = "Error al cancelar: " . $e->getMessage();
    }
}

// =============================================================================
// CONSULTA PARA OBTENER TODAS LAS RESERVAS ACTIVAS
// =============================================================================
// Se muestran reservas con fecha hoy o futura (fecha >= CURDATE())
// Se filtran las canceladas para no mostrarlas
// Se ordenan por fecha ascendente (más próximas primero)
//
// TABLAS IMPLICADAS:
// - reservas: tabla principal de reservas
// - socios: para obtener nombre y apellidos del socio que reservó
// - instalaciones: para obtener el nombre de la instalación reservada
//
// TIPOS DE JOIN:
// - JOIN (INNER JOIN): solo reservas que tengan socio e instalación válidos
$consultaReservas = $pdo->prepare("
    SELECT 
        reservas.id,
        reservas.fecha,
        reservas.hora_inicio,
        reservas.hora_fin,
        reservas.estado,
        socios.nombre AS socio_nombre,
        socios.apellidos AS socio_apellidos,
        instalaciones.nombre AS instalacion_nombre
    FROM reservas
    JOIN socios ON reservas.socio_id = socios.id
    JOIN instalaciones ON reservas.instalacion_id = instalaciones.id
    WHERE reservas.fecha >= CURDATE() 
      AND reservas.estado != 'cancelada'
    ORDER BY reservas.fecha ASC, reservas.hora_inicio ASC
");
$consultaReservas->execute();
$listaReservas = $consultaReservas->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Reservas - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- ========================================================================= -->
<!-- BARRA DE NAVEGACIÓN SUPERIOR                                              -->
<!-- ========================================================================= -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../dashboard.php">
            <i class="fas fa-arrow-left me-2"></i> Volver al Panel
        </a>
        <span class="text-white">Recepcionista: <?= $_SESSION['nombre'] ?></span>
    </div>
</nav>

<div class="container mt-4">
    <h2><i class="fas fa-calendar-check me-2"></i> Gestionar Reservas</h2>
    
    <!-- Mostrar mensajes de éxito o error -->
    <?php if($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Reservas activas</h5>
        </div>
        <div class="card-body">
            
            <!-- Botón para crear nueva reserva -->
            <a href="nueva_reserva.php" class="btn btn-success mb-3">
                <i class="fas fa-plus me-2"></i> Nueva Reserva
            </a>
            
            <!-- Tabla de reservas -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Socio</th>
                            <th>Instalación</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($listaReservas as $reserva): ?>
                        <tr>
                            <td><?= htmlspecialchars($reserva['socio_nombre'] . ' ' . $reserva['socio_apellidos']) ?></td>
                            <td><?= htmlspecialchars($reserva['instalacion_nombre']) ?></td>
                            <td><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></td>
                            <td><?= substr($reserva['hora_inicio'], 0, 5) ?> - <?= substr($reserva['hora_fin'], 0, 5) ?></td>
                            <td><span class="badge bg-success"><?= $reserva['estado'] ?></span></td>
                            <td>
                                <a href="?cancelar=<?= $reserva['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Cancelar esta reserva?')">
                                    Cancelar
                                </a>
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