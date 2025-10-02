<?php
// mostrar_datos.php - Muestra los datos del usuario según su ID recibido por GET
require_once 'conexion.php';

$usuario_id = isset($_GET['uid']) ? intval($_GET['uid']) : 0;

if ($usuario_id > 0) {
    $sql = "SELECT nombre, telefono, localidad, facultad, carrera, condiciones_medicas, email, dni FROM usuarios WHERE id = $usuario_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<h2>Datos del usuario</h2>";
    echo "<ul class='mostrar-datos-list'>";
        echo "<li><strong>Nombre:</strong> " . htmlspecialchars($user['nombre']) . "</li>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</li>";
        echo "<li><strong>DNI:</strong> " . htmlspecialchars($user['dni']) . "</li>";
        echo "<li><strong>Teléfono:</strong> " . htmlspecialchars($user['telefono']) . "</li>";
        echo "<li><strong>Localidad:</strong> " . htmlspecialchars($user['localidad']) . "</li>";
        echo "<li><strong>Facultad:</strong> " . htmlspecialchars($user['facultad']) . "</li>";
        echo "<li><strong>Carrera:</strong> " . htmlspecialchars($user['carrera']) . "</li>";
        echo "<li><strong>Condiciones médicas o alimenticias:</strong> " . htmlspecialchars($user['condiciones_medicas']) . "</li>";
        echo "</ul>";
    } else {
        echo "Usuario no encontrado.";
    }
} else {
    echo "ID de usuario no válido.";
}