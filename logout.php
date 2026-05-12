<?php
/**
 * LOGOUT - Cerrar sesión
 * 
 * Destruye la sesión y vuelve al login
 */

session_start();      // Iniciar sesión para poder manipularla
session_destroy();    // Borrar todos los datos de sesión
header('Location: login.php');  // Redirigir al login
exit();
?>