<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $deporte = $_POST['deporte'];
    $capacidad = $_POST['capacidad'];
    $precio_hora = $_POST['precio_hora'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    
    if (empty($nombre) || empty($capacidad) || empty($precio_hora)) {
        $error = "Todos los campos son obligatorios";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO instalaciones (nombre, deporte, capacidad, precio_hora, disponible) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $deporte, $capacidad, $precio_hora, $disponible]);
            $mensaje = "Instalación creada correctamente";
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Instalación - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="instalaciones.php">
            <i class="fas fa-arrow-left me-2"></i> Volver a Instalaciones
        </a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-plus me-2"></i> Nueva Instalación</h4>
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
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deporte</label>
                            <select name="deporte" class="form-select">
                                <option value="futbol">⚽ Fútbol</option>
                                <option value="baloncesto">🏀 Baloncesto</option>
                                <option value="tenis">🎾 Tenis</option>
                                <option value="padel">🏓 Pádel</option>
                                <option value="natacion">🏊 Natación</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacidad (personas)</label>
                            <input type="number" name="capacidad" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio por hora (€)</label>
                            <input type="number" step="0.01" name="precio_hora" class="form-control" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="disponible" class="form-check-input" checked>
                            <label class="form-check-label">Disponible</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>