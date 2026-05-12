<?php
/**
 * =============================================================================
 * NUEVO SOCIO - Crear cuenta para nuevos socios
 * =============================================================================
 * 
 * FUNCIONALIDAD:
 * - Permite dar de alta un nuevo socio en el sistema
 * - Inserta los datos en la tabla 'socios'
 * 
 * DIFERENCIA CON registro.php:
 * - Este archivo es para uso del recepcionista (backend)
 * - registro.php es para que los socios se registren solos (frontend)
 */

session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'recepcionista') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';

// =============================================================================
// PROCESAR FORMULARIO CUANDO SE ENVÍA
// =============================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recoger datos del formulario
    $dni = $_POST['dni'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $tipo_cuota = $_POST['tipo_cuota'];
    
    // Validar campos obligatorios
    if (empty($dni) || empty($nombre) || empty($apellidos)) {
        $error = "DNI, nombre y apellidos son obligatorios";
    } else {
        try {
            // INSERT en la tabla socios
            $consulta = $pdo->prepare("
                INSERT INTO socios 
                (dni, nombre, apellidos, telefono, email, fecha_nacimiento, fecha_alta, tipo_cuota, activo) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, 1)
            ");
            $consulta->execute([$dni, $nombre, $apellidos, $telefono, $email, $fecha_nacimiento, $tipo_cuota]);
            
            $mensaje = "✅ Socio creado correctamente";
            
            // Limpiar formulario después de guardar
            $_POST = [];
            
        } catch(PDOException $e) {
            $error = "❌ Error al crear socio: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Socio - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="socios.php">
            <i class="fas fa-arrow-left me-2"></i> Volver a Socios
        </a>
        <span class="text-white">Recepcionista: <?= $_SESSION['nombre'] ?></span>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i> Nuevo Socio</h4>
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
                            <label class="form-label">DNI *</label>
                            <input type="text" name="dni" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" name="apellidos" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" name="telefono" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fecha de nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Tipo de cuota</label>
                            <select name="tipo_cuota" class="form-select">
                                <option value="mensual">Mensual</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="anual">Anual</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i> Crear Socio
                        </button>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>