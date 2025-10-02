<?php
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
// Obtener eventos en los que el usuario está inscrito
$eventos = [];
$sql = "SELECT e.id, e.nombre FROM inscripciones i JOIN eventos e ON i.evento_id = e.id WHERE i.usuario_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $stmt->bind_result($evento_id, $evento_nombre);
    while ($stmt->fetch()) {
        $eventos[] = ['id' => $evento_id, 'nombre' => $evento_nombre];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Eventos</title>
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
        .main-title {
            text-align: center;
            font-size: 2.5rem;
            color: #fff;
            margin: 40px auto 24px auto;
        }
        .evento-item {
            background: #fff;
            color: #333;
            border-radius: 10px;
            padding: 18px 24px;
            margin: 0 0 16px 0;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .evento-item:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav-btns">
            <form action="inicio.php" method="get" class="m-0"><button type="submit" class="nav-btn nav-btn-home d-flex align-items-center justify-content-center"><i class="bi bi-house-fill"></i></button></form>
            <form action="eventos.php" method="get"><button type="submit" class="nav-btn">Eventos</button></form>
            <form action="mis_eventos.php" method="get"><button type="submit" class="nav-btn">Mis Eventos</button></form>
            <form action="misdatos.php" method="get"><button type="submit" class="nav-btn">Mis Datos</button></form>
            <form action="qr.php" method="get"><button type="submit" class="nav-btn">QR</button></form>
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
    <div class="main-title">Mis Eventos</div>
    <div class="evento-list">
        <?php if (count($eventos) === 0): ?>
            <div style="background:#c00; color:#fff; border-radius:10px; padding:18px 0; text-align:center; font-size:1.2rem; margin-bottom:24px; max-width:500px; margin-left:auto; margin-right:auto;">No estás inscrito en ningún evento.</div>
        <?php else: ?>
            <?php foreach ($eventos as $ev): ?>
                <div class="evento-item" onclick="location.href='eventopagina.php?id=<?php echo $ev['id']; ?>'">
                    <?php echo htmlspecialchars($ev['nombre']); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <script>
        document.getElementById('userIcon').addEventListener('click', function() {
            var menu = document.getElementById('userMenu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        });
        window.addEventListener('click', function(event) {
            var menu = document.getElementById('userMenu');
            if (!event.target.closest('.user-icon') && !event.target.closest('.user-menu')) {
                menu.style.display = 'none';
            }
        });
    </script>
</body>
</html>