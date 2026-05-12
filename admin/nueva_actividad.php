<?php
/**
 * NUEVA ACTIVIDAD - Solo para administradores
 * Crea una nueva actividad en la tabla 'actividades'
 */

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';
$deportes = ['futbol', 'baloncesto', 'tenis', 'padel', 'natacion'];
$dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

// Obtener instalaciones para el select
$instalaciones = $pdo->query("SELECT id, nombre FROM instalaciones ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nombre = trim($_POST['nombre']);
    $deporte = $_POST['deporte'];
    $instalacion_id = $_POST['instalacion_id'];
    $dia_semana = $_POST['dia_semana'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $plazas = $_POST['plazas'];
    $activa = isset($_POST['activa']) ? 1 : 0;
    
    if (empty($nombre) || empty($plazas)) {
        $error = "Nombre y plazas son obligatorios";
    } elseif ($hora_fin <= $hora_inicio) {
        $error = "La hora de fin debe ser después de la hora de inicio";
    } else {
        try {
            $sql = "INSERT INTO actividades (nombre, deporte, instalacion_id, dia_semana, hora_inicio, hora_fin, plazas, activa) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $deporte, $instalacion_id, $dia_semana, $hora_inicio, $hora_fin, $plazas, $activa]);
            
            $mensaje = "✅ Actividad creada correctamente";
            
            // Limpiar formulario
            $_POST = [];
            
        } catch(PDOException $e) {
            $error = "❌ Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Actividad - Polideportivo</title>
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
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-plus me-2"></i> Nueva Actividad</h4>
                </div>
                <div class="card-body">
                    
                    <?php if($mensaje): ?>
                        <div class="alert alert-success"><?= $mensaje ?></div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre de la actividad</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deporte</label>
                            <select name="deporte" class="form-select" required>
                                <?php foreach($deportes as $d): ?>
                                    <option value="<?= $d ?>"><?= ucfirst($d) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Instalación</label>
                            <select name="instalacion_id" class="form-select" required>
                                <?php foreach($instalaciones as $inst): ?>
                                    <option value="<?= $inst['id'] ?>"><?= htmlspecialchars($inst['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Día de la semana</label>
                            <select name="dia_semana" class="form-select" required>
                                <?php foreach($dias as $d): ?>
                                    <option value="<?= $d ?>"><?= ucfirst($d) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
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
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Plazas disponibles</label>
                            <input type="number" name="plazas" class="form-control" required>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" name="activa" class="form-check-input" checked>
                            <label class="form-check-label fw-semibold">Actividad activa</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i> Crear Actividad
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>