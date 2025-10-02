<?php
// agregar_evento.php - Formulario para agregar evento (solo admin)
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'conexion.php';
$usuario_id = $_SESSION['usuario_id'];
$nombre = '';
$rol = '';
$sql = "SELECT nombre, rol FROM usuarios WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $stmt->bind_result($nombre, $rol);
    $stmt->fetch();
    $stmt->close();
}
if ($rol !== 'admin') {
    header('Location: eventos.php');
    exit;
}
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_evento = trim($_POST['nombre_evento'] ?? '');
    if ($nombre_evento !== '') {
        $sql = "INSERT INTO eventos (nombre) VALUES (?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $nombre_evento);
            if ($stmt->execute()) {
                header('Location: eventos.php');
                exit;
            } else {
                $msg = 'Error al agregar el evento.';
            }
            $stmt->close();
        } else {
            $msg = 'Error en la base de datos.';
        }
    } else {
        $msg = 'El nombre es obligatorio.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Evento</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
        .form-container { background: #4e7ca7; border-radius: 8px; max-width: 400px; margin: 60px auto; padding: 32px; color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .form-title { font-size: 1.7rem; margin-bottom: 24px; text-align: center; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 1.1rem; }
        .form-input { width: 100%; padding: 10px; border-radius: 6px; border: none; font-size: 1rem; }
        .form-btn { background: #5ba4e5; color: #fff; border: none; border-radius: 8px; padding: 12px 28px; font-size: 1.1rem; cursor: pointer; width: 100%; margin-top: 10px; }
        .msg { text-align: center; margin-bottom: 12px; color: #fff; font-weight: 500; }
        .user-menu { display:none; position:absolute; top:90px; right:40px; background:#fff; color:#222; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.15); padding:24px 32px; z-index:9999; }
        .user-menu div { font-weight:600; font-size:1.1em; margin-bottom:16px; color:#174a6c; text-align:center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav-btns">
            <form action="inicio.php" method="get" class="m-0"><button type="submit" class="nav-btn nav-btn-home d-flex align-items-center justify-content-center"><i class="bi bi-house-fill"></i></button></form>
            <form action="eventos.php" method="get"><button type="submit" class="nav-btn">Eventos</button></form>
            <form action="mis_eventos.php" method="get"><button type="submit" class="nav-btn">Mis Eventos</button></form>
        </div>
        <span class="user-icon" id="userIcon"><i class="bi bi-person-circle"></i></span>
        <div class="user-menu" id="userMenu" style="display:none; position:absolute; top:90px; right:40px; background:#fff; color:#222; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.15); padding:24px 32px; z-index:9999;">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div style="font-weight:600; font-size:1.1em; margin-bottom:16px; color:#174a6c; text-align:center;">👤 <?php echo htmlspecialchars($nombre); ?></div>
            <?php endif; ?>
            <form action="logout.php" method="post">
                <button type="submit" class="nav-btn" style="background:#65bbf8; color:#fff; width:180px;">Cerrar sesión</button>
            </form>
        </div>
    </div>
    <div class="form-container">
        <div class="form-title">Agregar Evento</div>
        <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="nombre_evento">Nombre del evento</label>
                <input class="form-input" type="text" id="nombre_evento" name="nombre_evento" required>
            </div>
            <button class="form-btn" type="submit">Agregar</button>
        </form>
    </div>
    <script>
        document.getElementById('userIcon').addEventListener('click', function() {
            var menu = document.getElementById('userMenu');
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        });
    </script>
</body>
</html>