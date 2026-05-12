<?php
/**
 * GESTIONAR INSTALACIONES - Solo para administradores
 */

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';

// Guardar (INSERT o UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'guardar') {
    $id = $_POST['id'] ?? '';
    $nombre = trim($_POST['nombre']);
    $deporte = $_POST['deporte'];
    $capacidad = $_POST['capacidad'];
    $precio_hora = $_POST['precio_hora'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    
    if (empty($nombre) || empty($capacidad) || empty($precio_hora)) {
        $error = "Todos los campos son obligatorios";
    } else {
        try {
            if (empty($id)) {
                $stmt = $pdo->prepare("INSERT INTO instalaciones (nombre, deporte, capacidad, precio_hora, disponible) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $deporte, $capacidad, $precio_hora, $disponible]);
                $mensaje = "Instalación creada correctamente";
            } else {
                $stmt = $pdo->prepare("UPDATE instalaciones SET nombre=?, deporte=?, capacidad=?, precio_hora=?, disponible=? WHERE id=?");
                $stmt->execute([$nombre, $deporte, $capacidad, $precio_hora, $disponible, $id]);
                $mensaje = "Instalación actualizada correctamente";
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    try {
        $stmt = $pdo->prepare("DELETE FROM instalaciones WHERE id = ?");
        $stmt->execute([$id]);
        $mensaje = "Instalación eliminada correctamente";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Obtener todas las instalaciones
$instalaciones = $pdo->query("SELECT * FROM instalaciones ORDER BY deporte, nombre")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Instalaciones - Polideportivo</title>
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
    <h2><i class="fas fa-building me-2"></i> Gestionar Instalaciones</h2>
    
    <?php if($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Tabla de instalaciones -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Lista de Instalaciones</h5>
        </div>
        <div class="card-body">
            <a href="nueva_instalacion.php" class="btn btn-success mb-3">
                <i class="fas fa-plus me-2"></i> Nueva Instalación
            </a>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr><th>ID</th><th>Nombre</th><th>Deporte</th><th>Capacidad</th><th>Precio/hora</th><th>Disponible</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($instalaciones as $inst): ?>
                        <tr>
                            <td><?= $inst['id'] ?></td>
                            <td><?= htmlspecialchars($inst['nombre']) ?></td>
                            <td><?= $inst['deporte'] ?></td>
                            <td><?= $inst['capacidad'] ?></td>
                            <td><?= $inst['precio_hora'] ?> €</td>
                            <td><?= $inst['disponible'] ? '✅ Sí' : '❌ No' ?></td>
                            <td>
                                <a href="editar_instalacion.php?id=<?= $inst['id'] ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="?eliminar=<?= $inst['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar esta instalación?')">
                                    <i class="fas fa-trash"></i> Eliminar
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