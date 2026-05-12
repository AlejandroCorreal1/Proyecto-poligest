<?php
/**
 * LOGIN - Página de inicio de sesión
 * 
 * Los usuarios introducen email y contraseña
 * Si son correctos, se guarda la sesión y se redirige según su rol
 */

session_start();

// SI ya hay sesión activa, redirigir según el rol
if(isset($_SESSION['user_id'])) {
    // SI es socio, va a su panel; SI NO (else), va al panel de empleados
    if($_SESSION['rol'] == 'socio') {
        header('Location: socio/dashboard_socio.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

require_once __DIR__ . '/config/database.php';

$error = '';

// SI el formulario ha sido enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = $_POST['email'];
    $password = $_POST['contrasena'];
    
    /**
     * CONSULTA CON MARCADORES ?
     * 
     * Los signos ? son MARCadores de posición.
     * Sirven para evitar INYECCIÓN SQL (ataques de hackers).
     * 
     * EJEMPLO: WHERE email = ? AND contrasena = ?
     * El primer ? se reemplaza por $email
     * El segundo ? se reemplaza por $password
     * 
     * Esto es SEGURO porque PDO separa el código SQL de los datos
     */
    $sql = "
        SELECT 
            usuarios.id AS usuario_id,
            usuarios.empleado_id,
            usuarios.email,
            usuarios.rol,
            empleados.nombre,
            empleados.apellidos
        FROM usuarios
        INNER JOIN empleados ON usuarios.empleado_id = empleados.id
        WHERE usuarios.email = ? AND usuarios.contrasena = ? AND usuarios.activo = 1
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $password]);  // Los ? se reemplazan aquí
    $user = $stmt->fetch();
    
    // SI encontró un empleado
    if ($user) {
        // Guardar datos en sesión
        $_SESSION['user_id'] = $user['usuario_id'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['apellidos'] = $user['apellidos'];
        
        header('Location: dashboard.php');
        exit();
    }
    
    // SI NO encontró empleado, buscar en tabla socios
    $sql2 = "SELECT * FROM socios WHERE email = ? AND activo = 1";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([$email]);
    $socio = $stmt2->fetch();
    
    // SI encontró un socio
    if ($socio) {
        $_SESSION['user_id'] = $socio['id'];
        $_SESSION['rol'] = 'socio';
        $_SESSION['nombre'] = $socio['nombre'];
        $_SESSION['apellidos'] = $socio['apellidos'];
        
        header('Location: socio/dashboard_socio.php');
        exit();
    }
    
    // SI NO encontró nada (ni empleado ni socio)
    $error = "Email o contraseña incorrectos";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Polideportivo - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-image: url('https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=1600');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }
        .login-card {
            position: relative;
            z-index: 1;
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

<div class="container d-flex align-items-center min-vh-100">
    <div class="row justify-content-center w-100">
        <div class="col-md-5">
            
            <div class="login-card p-4 p-lg-5">
                
                <!-- Cabecera -->
                <div class="text-center mb-4">
                    <i class="fas fa-building fa-3x text-primary"></i>
                    <h2 class="fw-bold mt-2">Polideportivo</h2>
                    <p class="text-muted">Inicie sesión en su cuenta</p>
                </div>
                
                <!-- Mensaje de error -->
                <?php if($error): ?>
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulario de login -->
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo electrónico</label>
                        <input type="email" name="email" class="form-control form-control-lg" 
                               placeholder="usuario@ejemplo.com" required autofocus>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <input type="password" name="contrasena" class="form-control form-control-lg" 
                               placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                    </button>
                    
                    <!-- Enlace para registrarse (socios) -->
                    <div class="text-center">
                        <a href="registro.php" class="text-decoration-none">
                            <i class="fas fa-user-plus me-1"></i> ¿No tiene cuenta? Regístrese aquí
                        </a>
                    </div>
                    
                </form>
                
            </div>
        </div>
    </div>
</div>

</body>
</html>