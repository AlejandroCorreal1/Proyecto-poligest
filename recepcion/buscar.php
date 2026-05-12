<?php
/**
 * =============================================================================
 * BUSCAR SOCIOS - Solo para recepcionistas
 * =============================================================================
 * 
 * FUNCIONALIDAD:
 * - Buscar socios por DNI, nombre, apellidos o email
 * - Resultados se muestran en tiempo real
 * - Útil para encontrar un socio rápidamente
 * 
 * OPERACIONES:
 * - SELECT con LIKE: búsqueda parcial en múltiples campos
 * - Límite de 20 resultados para no saturar la pantalla
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

// Inicializar variables
$listaResultados = [];
$terminoBusqueda = '';

// =============================================================================
// PROCESAR BÚSQUEDA CUANDO SE ENVÍA EL FORMULARIO
// =============================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $terminoBusqueda = trim($_POST['busqueda']);
    
    // Solo buscar si no está vacío
    if (!empty($terminoBusqueda)) {
        
        // =====================================================================
        // CONSULTA DE BÚSQUEDA CON LIKE
        // =====================================================================
        // LIKE: permite búsqueda parcial (no tiene que coincidir exacto)
        // %termino%: busca que el término esté en cualquier posición
        // Ejemplo: buscar "juan" encuentra "Juan Pérez", "Juana", "juanito"
        //
        // Los signos ? son marcadores de posición (previenen inyección SQL)
        // Se reemplazan por $terminoBusqueda con % alrededor
        $consulta = $pdo->prepare("
            SELECT 
                socios.dni,
                socios.nombre,
                socios.apellidos,
                socios.telefono,
                socios.email,
                socios.tipo_cuota
            FROM socios 
            WHERE socios.dni LIKE ? 
               OR socios.nombre LIKE ? 
               OR socios.apellidos LIKE ? 
               OR socios.email LIKE ?
            LIMIT 20
        ");
        
        // Añadir % alrededor del término para búsqueda parcial
        $patron = "%$terminoBusqueda%";
        $consulta->execute([$patron, $patron, $patron, $patron]);
        $listaResultados = $consulta->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Socios - Polideportivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- ========================================================================= -->
<!-- BARRA DE NAVEGACIÓN SUPERIOR                                              -->
<!-- ========================================================================= -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../dashboard.php">
            <i class="fas fa-arrow-left me-2"></i> Volver al Panel
        </a>
        <span class="text-white">Recepcionista: <?= $_SESSION['nombre'] ?></span>
    </div>
</nav>

<div class="container mt-4">
    <h2><i class="fas fa-search me-2"></i> Buscar Socios</h2>
    
    <div class="card shadow-sm">
        <div class="card-body">
            
            <!-- ================================================================= -->
            <!-- FORMULARIO DE BÚSQUEDA                                             -->
            <!-- ================================================================= -->
            <form method="POST">
                <div class="input-group">
                    <input type="text" name="busqueda" class="form-control form-control-lg" 
                           placeholder="Buscar por DNI, nombre, apellidos o email..." 
                           value="<?= htmlspecialchars($terminoBusqueda) ?>" autofocus>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
                <small class="text-muted">Ejemplo: "juan", "12345678A", "garcia"</small>
            </form>
            
            <!-- ================================================================= -->
            <!-- MOSTRAR RESULTADOS DE LA BÚSQUEDA                                 -->
            <!-- ================================================================= -->
            <?php if($listaResultados): ?>
                <hr>
                <h5>Resultados encontrados: <?= count($listaResultados) ?></h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>DNI</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Cuota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($listaResultados as $socio): ?>
                            <tr>
                                <td><?= $socio['dni'] ?></td>
                                <td><?= htmlspecialchars($socio['nombre']) ?></td>
                                <td><?= htmlspecialchars($socio['apellidos']) ?></td>
                                <td><?= $socio['telefono'] ?></td>
                                <td><?= $socio['email'] ?></td>
                                <td><?= $socio['tipo_cuota'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($terminoBusqueda)): ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i> No se encontraron socios con "<?= htmlspecialchars($terminoBusqueda) ?>"
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

</body>
</html>