<?php
/**
 * =============================================================================
 * GESTIONAR SOCIOS - Solo para recepcionistas
 * =============================================================================
 * 
 * FUNCIONALIDAD:
 * - Ver listado completo de socios activos
 * - Dar de alta nuevos socios
 * - Dar de baja (eliminar) socios
 * 
 * OPERACIONES:
 * - SELECT: Muestra todos los socios activos
 * - UPDATE (baja): Cambia activo a 0 (baja lógica)
 * - Enlace a nuevo_socio.php para INSERT
 * 
 * BAJA LÓGICA vs BAJA FÍSICA:
 * - Se usa UPDATE en lugar de DELETE para conservar el historial
 * - El socio se marca como inactivo pero sus reservas pasadas se conservan
 */

session_start();

// =============================================================================
// CONTROL DE ACCESO: Solo recepcionistas pueden entrar
// =============================================================================
if(!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'recepcionista') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/database.php';

$mensaje = '';
$error = '';

// =============================================================================
// PROCESAR ELIMINACIÓN (DAR DE BAJA) DE UN SOCIO
// =============================================================================
// Se recibe por GET el ID del socio a eliminar
if (isset($_GET['eliminar'])) {
    $id_socio = $_GET['eliminar'];
    
    try {
        // BAJA LÓGICA: cambiar activo a 0 en lugar de eliminar el registro
        // Esto conserva el historial de reservas del socio
        $consulta = $pdo->prepare("UPDATE socios SET activo = 0 WHERE id = ?");
        $consulta->execute([$id_socio]);
        $mensaje = "Socio dado de baja correctamente";
        
        // Redirigir para evitar reenvío del formulario
        header('Location: socios.php');
        exit();
        
    } catch(PDOException $e) {
        $error = "Error al dar de baja: " . $e->getMessage();
    }
}

// =============================================================================
// CONSULTA PARA OBTENER TODOS LOS SOCIOS ACTIVOS
// =============================================================================
$consultaSocios = $pdo->query("
    SELECT 
        socios.id,
        socios.dni,
        socios.nombre,
        socios.apellidos,
        socios.telefono,
        socios.email,
        socios.tipo_cuota,
        socios.fecha_alta,
        socios.fecha_nacimiento
    FROM socios 
    WHERE socios.activo = 1 
    ORDER BY socios.apellidos, socios.nombre
");
$listaSocios = $consultaSocios->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Socios - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../dashboard.php">
            <i class="fas fa-arrow-left me-2"></i> Volver al Panel
        </a>
        <span class="text-white">Recepcionista: <?= $_SESSION['nombre'] ?></span>
    </div>
</nav>

<div class="container mt-4">
    <h2><i class="fas fa-users me-2"></i> Gestionar Socios</h2>
    
    <!-- Mostrar mensajes -->
    <?php if($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Lista de Socios</h5>
        </div>
        <div class="card-body">
            
            <!-- Botón para crear nuevo socio -->
            <a href="nuevo_socio.php" class="btn btn-success mb-3">
                <i class="fas fa-plus me-2"></i> Nuevo Socio
            </a>
            
            <!-- Tabla de socios con columna de acciones -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Cuota</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($listaSocios as $socio): ?>
                        <tr>
                            <td><?= $socio['id'] ?></td>
                            <td><?= $socio['dni'] ?></td>
                            <td><?= htmlspecialchars($socio['nombre']) ?></td>
                            <td><?= htmlspecialchars($socio['apellidos']) ?></td>
                            <td><?= $socio['telefono'] ?><td>
                            <td><?= $socio['email'] ?></td>
                            <td><?= $socio['tipo_cuota'] ?></td>
                            <td>
                                <!-- Botón para eliminar (dar de baja) -->
                                <a href="?eliminar=<?= $socio['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('¿Dar de baja a <?= htmlspecialchars($socio['nombre']) ?> <?= htmlspecialchars($socio['apellidos']) ?>?')">
                                    <i class="fas fa-user-slash me-1"></i> Dar de baja
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Mostrar mensaje si no hay socios -->
            <?php if(count($listaSocios) == 0): ?>
                <div class="alert alert-info mt-3">No hay socios registrados</div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

</body>
</html>