<?php
/**
 * =============================================================================
 * NUEVA RESERVA - Para recepcionistas
 * =============================================================================
 * 
 * FUNCIONALIDAD:
 * - Permite al recepcionista crear reservas para cualquier socio
 * - Selecciona socio, instalación, fecha y hora
 * - Valida que no haya conflicto de horario
 */

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'recepcionista') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';

// Obtener lista de socios para el select
$listaSocios = $pdo->query("
    SELECT socios.id, socios.nombre, socios.apellidos, socios.dni 
    FROM socios 
    WHERE socios.activo = 1 
    ORDER BY socios.apellidos, socios.nombre
")->fetchAll();

// Obtener instalaciones disponibles
$listaInstalaciones = $pdo->query("
    SELECT instalaciones.id, instalaciones.nombre, instalaciones.deporte, instalaciones.precio_hora 
    FROM instalaciones 
    WHERE instalaciones.disponible = 1 
    ORDER BY instalaciones.deporte
")->fetchAll();

// =============================================================================
// PROCESAR FORMULARIO
// =============================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_socio = $_POST['socio_id'];
    $id_instalacion = $_POST['instalacion_id'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    
    // Validaciones
    if ($fecha < date('Y-m-d')) {
        $error = "No se puede reservar en una fecha pasada";
    } elseif ($hora_fin <= $hora_inicio) {
        $error = "La hora de fin debe ser después de la hora de inicio";
    } else {
        
        // Verificar si la instalación ya está reservada en ese horario
        $consulta = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM reservas 
            WHERE reservas.instalacion_id = ? 
              AND reservas.fecha = ? 
              AND reservas.estado != 'cancelada'
              AND (
                  (reservas.hora_inicio < ? AND reservas.hora_fin > ?) OR
                  (reservas.hora_inicio >= ? AND reservas.hora_inicio < ?)
              )
        ");
        $consulta->execute([$id_instalacion, $fecha, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin]);
        $resultado = $consulta->fetch();
        
        if ($resultado['total'] > 0) {
            $error = "La instalación ya está reservada en ese horario";
        } else {
            // Guardar reserva
            $guardar = $pdo->prepare("
                INSERT INTO reservas 
                (socio_id, instalacion_id, fecha, hora_inicio, hora_fin, estado, fecha_reserva) 
                VALUES (?, ?, ?, ?, ?, 'confirmada', NOW())
            ");
            $guardar->execute([$id_socio, $id_instalacion, $fecha, $hora_inicio, $hora_fin]);
            $mensaje = "✅ Reserva creada correctamente";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Reserva - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="reservas.php">
            <i class="fas fa-arrow-left me-2"></i> Volver a Reservas
        </a>
        <span class="text-white">Recepcionista: <?= $_SESSION['nombre'] ?></span>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i> Nueva Reserva</h4>
                </div>
                <div class="card-body">
                    
                    <?php if($mensaje): ?>
                        <div class="alert alert-success"><?= $mensaje ?></div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        
                        <!-- Seleccionar socio -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Socio</label>
                            <select name="socio_id" class="form-select" required>
                                <option value="">Seleccione un socio</option>
                                <?php foreach($listaSocios as $socio): ?>
                                    <option value="<?= $socio['id'] ?>">
                                        <?= htmlspecialchars($socio['nombre'] . ' ' . $socio['apellidos'] . ' (' . $socio['dni'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Seleccionar instalación -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Instalación</label>
                            <select name="instalacion_id" class="form-select" required>
                                <option value="">Seleccione una instalación</option>
                                <?php foreach($listaInstalaciones as $inst): ?>
                                    <option value="<?= $inst['id'] ?>">
                                        <?= htmlspecialchars($inst['nombre']) ?> (<?= ucfirst($inst['deporte']) ?>) - <?= $inst['precio_hora'] ?> €/h
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Fecha -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Fecha</label>
                            <input type="date" name="fecha" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <!-- Horario -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Hora inicio</label>
                                <input type="time" name="hora_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Hora fin</label>
                                <input type="time" name="hora_fin" class="form-control" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check-circle me-2"></i> Crear Reserva
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>