<?php
/**
 * =============================================================================
 * EDITAR ACTIVIDAD - Solo para administradores
 * =============================================================================
 * 
 * FUNCIONALIDAD:
 * - Permite modificar una actividad existente
 * - Actualiza los datos en la tabla 'actividades'
 * - Valida que los datos sean correctos
 * 
 * PROCESO:
 * 1. Recibe el ID de la actividad por GET
 * 2. Carga los datos actuales de la actividad
 * 3. Muestra el formulario con los datos cargados
 * 4. Al enviar, actualiza (UPDATE) la actividad
 * 
 * CONTROL DE ACCESO:
 * - Solo administradores pueden acceder
 */

session_start();

// Verificar que sea administrador
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

// =============================================================================
// OBTENER EL ID DE LA ACTIVIDAD A EDITAR
// =============================================================================
$id = $_GET['id'] ?? 0;

// Si no hay ID válido, redirigir a la lista de actividades
if($id <= 0) {
    header('Location: actividades.php');
    exit();
}

// =============================================================================
// CARGAR LOS DATOS ACTUALES DE LA ACTIVIDAD
// =============================================================================
$stmt = $pdo->prepare("SELECT * FROM actividades WHERE id = ?");
$stmt->execute([$id]);
$actividad = $stmt->fetch();

// Si la actividad no existe, redirigir
if(!$actividad) {
    header('Location: actividades.php');
    exit();
}

// =============================================================================
// OBTENER LISTA DE INSTALACIONES PARA EL SELECT
// =============================================================================
$instalaciones = $pdo->query("SELECT id, nombre FROM instalaciones ORDER BY nombre")->fetchAll();

// =============================================================================
// PROCESAR FORMULARIO CUANDO SE ENVÍA (UPDATE)
// =============================================================================
$mensaje = '';
$error = '';
$deportes = ['futbol', 'baloncesto', 'tenis', 'padel', 'natacion'];
$dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recoger datos del formulario
    $nombre = trim($_POST['nombre']);
    $deporte = $_POST['deporte'];
    $instalacion_id = $_POST['instalacion_id'];
    $dia_semana = $_POST['dia_semana'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $plazas = $_POST['plazas'];
    $activa = isset($_POST['activa']) ? 1 : 0;
    
    // Validar campos obligatorios
    if (empty($nombre) || empty($plazas)) {
        $error = "Nombre y plazas son obligatorios";
    }
    // Validar que hora_fin sea después de hora_inicio
    elseif ($hora_fin <= $hora_inicio) {
        $error = "La hora de fin debe ser después de la hora de inicio";
    }
    else {
        try {
            // Actualizar la actividad
            $sql = "UPDATE actividades SET 
                        nombre = ?, 
                        deporte = ?, 
                        instalacion_id = ?, 
                        dia_semana = ?, 
                        hora_inicio = ?, 
                        hora_fin = ?, 
                        plazas = ?, 
                        activa = ? 
                    WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $deporte, $instalacion_id, $dia_semana, $hora_inicio, $hora_fin, $plazas, $activa, $id]);
            
            $mensaje = "✅ Actividad actualizada correctamente";
            
            // Recargar los datos actualizados
            $stmt2 = $pdo->prepare("SELECT * FROM actividades WHERE id = ?");
            $stmt2->execute([$id]);
            $actividad = $stmt2->fetch();
            
        } catch(PDOException $e) {
            $error = "❌ Error al actualizar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Actividad - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="actividades.php">
            <i class="fas fa-arrow-left me-2"></i> Volver a Actividades
        </a>
        <span class="text-white">Administrador: <?= $_SESSION['nombre'] ?></span>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Editar Actividad</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Mensajes -->
                    <?php if($mensaje): ?>
                        <div class="alert alert-success"><?= $mensaje ?></div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <!-- Formulario de edición -->
                    <form method="POST">
                        
                        <!-- Nombre de la actividad -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre de la actividad</label>
                            <input type="text" name="nombre" class="form-control" 
                                   value="<?= htmlspecialchars($actividad['nombre']) ?>" required>
                        </div>
                        
                        <!-- Deporte -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deporte</label>
                            <select name="deporte" class="form-select" required>
                                <?php foreach($deportes as $d): ?>
                                    <option value="<?= $d ?>" <?= $actividad['deporte'] == $d ? 'selected' : '' ?>>
                                        <?= ucfirst($d) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Instalación -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Instalación</label>
                            <select name="instalacion_id" class="form-select" required>
                                <?php foreach($instalaciones as $inst): ?>
                                    <option value="<?= $inst['id'] ?>" <?= $actividad['instalacion_id'] == $inst['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($inst['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Día de la semana -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Día de la semana</label>
                            <select name="dia_semana" class="form-select" required>
                                <?php foreach($dias as $d): ?>
                                    <option value="<?= $d ?>" <?= $actividad['dia_semana'] == $d ? 'selected' : '' ?>>
                                        <?= ucfirst($d) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Horario -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Hora inicio</label>
                                <input type="time" name="hora_inicio" class="form-control" 
                                       value="<?= $actividad['hora_inicio'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Hora fin</label>
                                <input type="time" name="hora_fin" class="form-control" 
                                       value="<?= $actividad['hora_fin'] ?>" required>
                            </div>
                        </div>
                        
                        <!-- Plazas -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Plazas disponibles</label>
                            <input type="number" name="plazas" class="form-control" 
                                   value="<?= $actividad['plazas'] ?>" required>
                        </div>
                        
                        <!-- Activa -->
                        <div class="mb-4 form-check">
                            <input type="checkbox" name="activa" class="form-check-input" 
                                   <?= $actividad['activa'] ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold">Actividad activa</label>
                        </div>
                        
                        <!-- Botones -->
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                        
                        <a href="actividades.php" class="btn btn-secondary w-100 mt-2">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>