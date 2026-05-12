<?php
/**
 * ALTA EMPLEADO - Solo para administradores
 */

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$exito = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];
    
    try {
        $pdo->beginTransaction();
        
        // Insertar en empleados
        $stmt = $pdo->prepare("
            INSERT INTO empleados (nombre, apellidos, dni, telefono, email, fecha_alta, activo) 
            VALUES (?, ?, ?, ?, ?, CURDATE(), 1)
        ");
        $stmt->execute([$nombre, $apellidos, $dni, $telefono, $email]);
        $empleadoId = $pdo->lastInsertId();
        
        // Insertar en usuarios
        $stmt2 = $pdo->prepare("
            INSERT INTO usuarios (empleado_id, email, contrasena, rol, activo) 
            VALUES (?, ?, ?, ?, 1)
        ");
        $stmt2->execute([$empleadoId, $email, $contrasena, $rol]);
        
        $pdo->commit();
        $exito = "✅ Empleado creado correctamente";
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alta de Empleado - Polideportivo</title>
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

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i> Dar de alta empleado</h4>
                </div>
                <div class="card-body">
                    
                    <?php if($exito): ?>
                        <div class="alert alert-success"><?= $exito ?></div>
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
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">DNI</label>
                            <input type="text" name="dni" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="contrasena" class="form-control" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Rol</label>
                            <select name="rol" class="form-select">
                                <option value="recepcionista">📞 Recepcionista</option>
                                <option value="monitor">🏋️ Monitor</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Crear empleado</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>