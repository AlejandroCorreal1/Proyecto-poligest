<?php
/**
 * =============================================================================
 * NUEVA RESERVA - Para socios
 * =============================================================================
 * 
 * FUNCIONALIDAD:
 * - Los socios pueden reservar instalaciones deportivas
 * - Seleccionan: instalación, fecha, hora de inicio y hora de fin
 * - El sistema valida que no esté ocupado
 * - Guarda la reserva en la tabla 'reservas'
 * 
 * VALIDACIONES:
 * - No se puede reservar en el pasado
 * - No se puede reservar una instalación ya ocupada
 * - La hora de fin debe ser después de la hora de inicio
 * 
 * CONTROL DE ACCESO:
 * - Solo socios pueden acceder
 */

session_start();

// Verificar que sea socio
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'socio') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';

// Obtener todas las instalaciones disponibles
$instalaciones = $pdo->query("SELECT * FROM instalaciones WHERE disponible = 1 ORDER BY deporte, nombre")->fetchAll();

// =============================================================================
// PROCESAR FORMULARIO (guardar reserva)
// =============================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $instalacion_id = $_POST['instalacion_id'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $socio_id = $_SESSION['socio_id'];
    
    // =========================================================================
    // VALIDACIONES
    // =========================================================================
    
    // 1. Validar que la fecha no sea en el pasado
    $fecha_actual = date('Y-m-d');
    if ($fecha < $fecha_actual) {
        $error = "No se puede reservar en una fecha pasada";
    }
    
    // 2. Validar que la hora de fin sea después de la hora de inicio
    elseif ($hora_fin <= $hora_inicio) {
        $error = "La hora de fin debe ser después de la hora de inicio";
    }
    
    // 3. Validar que la instalación existe y está disponible
    else {
        $checkInstalacion = $pdo->prepare("SELECT * FROM instalaciones WHERE id = ? AND disponible = 1");
        $checkInstalacion->execute([$instalacion_id]);
        if (!$checkInstalacion->fetch()) {
            $error = "La instalación no está disponible";
        }
    }
    
    // 4. Validar que no haya conflicto de horario
    if (empty($error)) {
        $sql = "
            SELECT * FROM reservas 
            WHERE instalacion_id = ? 
            AND fecha = ? 
            AND estado != 'cancelada'
            AND (
                (hora_inicio < ? AND hora_fin > ?) OR
                (hora_inicio >= ? AND hora_inicio < ?)
            )
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$instalacion_id, $fecha, $hora_fin, $hora_inicio, $hora_inicio, $hora_fin]);
        
        if ($stmt->rowCount() > 0) {
            $error = "La instalación ya está reservada en ese horario";
        }
    }
    
    // 5. Guardar reserva
    if (empty($error)) {
        try {
            $sql = "INSERT INTO reservas (socio_id, instalacion_id, fecha, hora_inicio, hora_fin, estado, fecha_reserva) 
                    VALUES (?, ?, ?, ?, ?, 'confirmada', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$socio_id, $instalacion_id, $fecha, $hora_inicio, $hora_fin]);
            
            $mensaje = "✅ Reserva realizada correctamente";
            
        } catch(PDOException $e) {
            $error = "Error al guardar: " . $e->getMessage();
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
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
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

<div class="container mt-5">
    <div class="form-container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i> Nueva Reserva</h4>
            </div>
            <div class="card-body">
                
                <!-- Mensajes -->
                <?php if($mensaje): ?>
                    <div class="alert alert-success"><?= $mensaje ?></div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <!-- Formulario de reserva -->
                <form method="POST">
                    
                    <!-- Seleccionar instalación -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instalación</label>
                        <select name="instalacion_id" class="form-select" required>
                            <option value="">Seleccione una instalación</option>
                            <?php foreach($instalaciones as $inst): ?>
                                <option value="<?= $inst['id'] ?>">
                                    <?= htmlspecialchars($inst['nombre']) ?> - <?= ucfirst($inst['deporte']) ?> (<?= $inst['precio_hora'] ?> €/hora)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Fecha -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha</label>
                        <input type="date" name="fecha" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <!-- Hora inicio y fin -->
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
                        <i class="fas fa-check-circle me-2"></i> Confirmar Reserva
                    </button>
                    
                </form>
                
                <hr class="my-4">
                
                <!-- Horario de apertura -->
                <div class="text-center">
                    <p class="text-muted small mb-0">
                        <i class="fas fa-clock me-1"></i> Horario: 08:00 - 22:00
                    </p>
                </div>
                
            </div>
        </div>
    </div>
</div>

</body>
</html>