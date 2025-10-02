<?php
session_start();
require_once 'conexion.php'; // debe definir $conn (mysqli)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['nombre'] ?? '');
    $user_pass = $_POST['password'] ?? '';

    if ($usuario === '' || $user_pass === '') {
        $error = 'Completa todos los campos.';
    } else {
        $sql = "SELECT id, nombre, password, email, rol FROM usuarios WHERE nombre = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $usuario);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $nombre_db, $pass_db, $email, $rol);
                $stmt->fetch();

                if (password_verify($user_pass, $pass_db)) {
                    // Guardar usuario en sesión
                    $_SESSION['usuario_id'] = $id;
                    $_SESSION['usuario_email'] = $email;
                    $_SESSION['usuario_nombre'] = $nombre_db;
                    $_SESSION['usuario_rol'] = $rol;
                    header("Location: inicio.php");
                    exit;
                } else {
                    $error = 'Contraseña incorrecta.';
                }
            } else {
                $error = 'Usuario no encontrado.';
            }
            $stmt->close();
        } else {
            $error = 'Error en la consulta.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Super QR - Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <div class="container">
        <h1>Super QR</h1>
        <div class="form-box">
            <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
            <form action="login.php" method="post">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="nombre" required>
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <div style="display: flex; gap: 10px; justify-content: flex-start;">
                    <button type="submit">Registrarse</button>
                    <button type="button" onclick="window.location.href='index.php'">Volver</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>