<?php
require_once 'config/database.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $contrasena = $_POST['contrasena'];  // ← sin hash, texto plano
    
    try {
        // Insertar socio
        $stmt = $pdo->prepare("INSERT INTO socios (dni, nombre, apellidos, telefono, email, fecha_alta, activo) VALUES (?, ?, ?, ?, ?, CURDATE(), 1)");
        $stmt->execute([$dni, $nombre, $apellidos, $telefono, $email]);
        $socioId = $pdo->lastInsertId();
        
        // Insertar usuario (con contraseña en texto plano)
        $stmt2 = $pdo->prepare("INSERT INTO usuarios (email, contrasena, rol, socio_id) VALUES (?, ?, 'cliente', ?)");
        $stmt2->execute([$email, $contrasena, $socioId]);
        
        $mensaje = "<div class='alert alert-success'>✅ Registro exitoso. <a href='login.php'>Iniciar sesión</a></div>";
    } catch(PDOException $e) {
        $mensaje = "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Polideportivo</title>
    <!-- BOOTSTRAP 5 (solo añadir esto y ya funciona) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📝 Registro de Socio</h4>
                </div>
                <div class="card-body">
                    
                    <?php echo $mensaje; ?>
                    
                    <form method="POST">
                        <!-- DNI -->
                        <div class="mb-3">
                            <label class="form-label">DNI</label>
                            <input type="text" name="dni" class="form-control" required>
                        </div>
                        
                        <!-- Nombre y Apellidos en fila -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellidos</label>
                                <input type="text" name="apellidos" class="form-control" required>
                            </div>
                        </div>
                        
                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" required>
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <!-- Contraseña (con nombre contrasena) -->
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="contrasena" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>