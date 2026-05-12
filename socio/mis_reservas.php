<?php
/**
 * =============================================================================
 * MIS RESERVAS - Para socios
 * =============================================================================
 * 
 * FUNCIONALIDAD:
 * - Muestra todas las reservas del socio logueado
 * - Separa entre reservas pendientes y pasadas
 * - Permite cancelar reservas futuras
 * 
 * CONTROL DE ACCESO:
 * - Solo socios pueden acceder
 */

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'socio') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';

$socio_id = $_SESSION['socio_id'];

// =============================================================================
// CANCELAR RESERVA
// =============================================================================
if (isset($_GET['cancelar'])) {
    $reserva_id = $_GET['cancelar'];
    
    try {
        $stmt = $pdo->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id = ? AND socio_id = ? AND fecha >= CURDATE()");
        $stmt->execute([$reserva_id, $socio_id]);
        $mensaje = "Reserva cancelada correctamente";
    } catch(PDOException $e) {
        $error = "Error al cancelar: " . $e->getMessage();
    }
}

// =============================================================================
// OBTENER RESERVAS DEL SOCIO
// =============================================================================

// Reservas activas (hoy o en el futuro, no canceladas)
$reservasActivas = $pdo->prepare("
    SELECT r.*, i.nombre as instalacion_nombre, i.deporte 
    FROM reservas r
    JOIN instalaciones i ON r.instalacion_id = i.id
    WHERE r.socio_id = ? AND r.fecha >= CURDATE() AND r.estado != 'cancelada'
    ORDER BY r.fecha ASC, r.hora_inicio ASC
");
$reservasActivas->execute([$socio_id]);
$reservasActivas = $reservasActivas->fetchAll();

// Reservas pasadas o canceladas (historial)
$reservasPasadas = $pdo->prepare("
    SELECT r.*, i.nombre as instalacion_nombre, i.deporte 
    FROM reservas r
    JOIN instalaciones i ON r.instalacion_id = i.id
    WHERE r.socio_id = ? AND (r.fecha < CURDATE() OR r.estado = 'cancelada')
    ORDER BY r.fecha DESC, r.hora_inicio DESC
    LIMIT 10
");
$reservasPasadas->execute([$socio_id]);
$reservasPasadas = $reservasPasadas->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard_socio.php">
            <i class="fas fa-arrow-left me-2"></i> Volver a Mi Panel
        </a>
        <div>
            <span class="text-white me-3">
                <i class="fas fa-user me-1"></i> <?= $_SESSION['nombre'] . ' ' . $_SESSION['apellidos'] ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2><i class="fas fa-calendar-alt me-2"></i> Mis Reservas</h2>
    
    <!-- Mensajes -->
    <?php if($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Reservas activas -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Reservas activas</h5>
        </div>
        <div class="card-body">
            <?php if(count($reservasActivas) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr><th>Instalación</th><th>Deporte</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Acción</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($reservasActivas as $res): ?>
                            <tr>
                                <td><?= htmlspecialchars($res['instalacion_nombre']) ?></td>
                                <td><?= ucfirst($res['deporte']) ?></td>
                                <td><?= date('d/m/Y', strtotime($res['fecha'])) ?></td>
                                <td><?= substr($res['hora_inicio'], 0, 5) ?> - <?= substr($res['hora_fin'], 0, 5) ?></td>
                                <td>
                                    <?php if($res['estado'] == 'confirmada'): ?>
                                        <span class="badge bg-success">Confirmada</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><?= $res['estado'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($res['fecha'] >= date('Y-m-d') && $res['estado'] != 'cancelada'): ?>
                                        <a href="?cancelar=<?= $res['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Cancelar esta reserva?')">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">No tienes reservas activas</p>
                <div class="text-center">
                    <a href="nueva_reserva.php" class="btn btn-primary">Hacer una reserva</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Historial de reservas -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i> Historial</h5>
        </div>
        <div class="card-body">
            <?php if(count($reservasPasadas) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Instalación</th><th>Fecha</th><th>Hora</th><th>Estado</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($reservasPasadas as $res): ?>
                            <tr>
                                <td><?= htmlspecialchars($res['instalacion_nombre']) ?></td>
                                <td><?= date('d/m/Y', strtotime($res['fecha'])) ?></td>
                                <td><?= substr($res['hora_inicio'], 0, 5) ?> - <?= substr($res['hora_fin'], 0, 5) ?></td>
                                <td>
                                    <?php if($res['estado'] == 'cancelada'): ?>
                                        <span class="badge bg-danger">Cancelada</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Completada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center">No hay reservas en el historial</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>