<?php
// inicio.php - Página de inicio con diseño UTN
session_start();
require_once 'conexion.php';

$usuario_nombre = '';
if (isset($_SESSION['usuario_id'])) {
    $id = $_SESSION['usuario_id'];
    $sql = "SELECT nombre FROM usuarios WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $usuario_nombre = $row['nombre'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Super QR - Inicio</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Estilos trasladados a global.css -->
</head>
<body>
    <div class="header">
        <div class="nav-btns">
            <form action="inicio.php" method="get" class="m-0"><button type="submit" class="nav-btn nav-btn-home d-flex align-items-center justify-content-center"><i class="bi bi-house-fill"></i></button></form>
            <form action="eventos.php" method="get"><button type="submit" class="nav-btn">Eventos</button></form>
            <form action="misdatos.php" method="get"><button type="submit" class="nav-btn">Mis Datos</button></form>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <form action="detalle_usuario.php" method="get"><button type="submit" class="nav-btn">Detalle de usuario</button></form>
            <?php endif; ?>
            <form action="qr.php" method="get"><button type="submit" class="nav-btn">QR</button></form>
        </div>
        <span class="user-icon" id="userIcon"><i class="bi bi-person-circle"></i></span>
        <div class="user-menu" id="userMenu" style="display:none; position:absolute; top:90px; right:40px; background:#fff; color:#222; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.15); padding:24px 32px; z-index:9999;">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div style="font-weight:600; font-size:1.1em; margin-bottom:16px; color:#174a6c; text-align:center;">👤 <?php echo htmlspecialchars($usuario_nombre); ?></div>
            <?php endif; ?>
            <form action="logout.php" method="post">
                <button type="submit" class="nav-btn" style="background:#65bbf8; color:#fff; width:180px;">Cerrar sesión</button>
            </form>
        </div>
    </div>
    <script>
        const userIcon = document.getElementById('userIcon');
        const userMenu = document.getElementById('userMenu');
        userIcon.addEventListener('click', function(e) {
            userMenu.style.display = userMenu.style.display === 'block' ? 'none' : 'block';
            e.stopPropagation();
        });
        document.addEventListener('click', function() {
            userMenu.style.display = 'none';
        });
    </script>
</body>
</html>
<style>
        body {
            background: #174a6c;
            margin: 0;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background: #4876a5ff;
        }
        .user-icon {
            font-size: 2.5rem;
            color: #333;
        }
        .nav-btns {
            display: flex;
            gap: 40px;
        }
        .nav-btn {
            background: #174a6c;
            color: #fff;
            border: none;
            border-radius: 7px;
            font-size: 1em;
            font-weight: 500;
            padding: 10px 32px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: background 0.2s;
        }
        .nav-btn:hover {
            background: #205b85;
        }
        .topbar {
            background: #5ba4e5;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
        }
        .utn-logo {
            text-align: center;
            flex: 1;
        }
        .utn-logo img {
            height: 50px;
        }
        .utn-logo .utn-title {
            font-size: 2.2rem;
            font-weight: bold;
            color: #000;
            margin-top: 2px;
        }
        .utn-logo .utn-sub {
            font-size: 0.9rem;
            color: #222;
            margin-top: -8px;
        }
        .eventos-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 100px);
            color: #fff;
        }
        .eventos-title {
            font-size: 2rem;
            margin-bottom: 24px;
        }
    </style>
