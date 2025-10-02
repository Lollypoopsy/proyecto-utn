            .header {
                flex-direction: column;
                gap: 10px;
                padding: 10px 10px;
            }
            .nav-btns {
                gap: 10px;
                flex-wrap: wrap;
            }
        }
        @media (max-width: 500px) {
            .nav-btn {
                font-size: 0.95em;
                padding: 8px 10px;
            }
            .user-icon {
                font-size: 2em;
            }
        }
    /* Estilos trasladados a global.css */
<?php
// dashboard.php - Página de bienvenida tras iniciar sesión
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Super QR - Bienvenido</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenido a Super QR</h1>
        <div class="form-box">
            <p>Has iniciado sesión correctamente.</p>
            <a href="logout.php" style="color:#6bb6ff;">Cerrar sesión</a>
        </div>
    </div>
</body>
</html>
