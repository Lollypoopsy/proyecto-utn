<?php
// eventos.php - Página de eventos
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
// Conexión a la base de datos
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
if ($rol == 'admin') {
    $rol_texto = 'admin';
} else {
    $rol_texto = 'usuario';
}
$msg = '';
// Eliminar evento si es admin y se envía id por GET
if ($rol_texto === 'admin' && isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $del_stmt = $conn->prepare("DELETE FROM eventos WHERE id = ?");
    if ($del_stmt) {
        $del_stmt->bind_param('i', $delete_id);
        if ($del_stmt->execute()) {
            $msg = 'Evento eliminado correctamente.';
            // Redirigir para limpiar la URL y actualizar la lista
            header('Location: eventos.php');
            exit;
        } else {
            $msg = 'Error al eliminar el evento.';
        }
        $del_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos UTN</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #4876a5ff;
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
</head>
<body>
    <div class="header">
        <div class="nav-btns">
            <form action="inicio.php" method="get" class="m-0"><button type="submit" class="nav-btn nav-btn-home d-flex align-items-center justify-content-center"><i class="bi bi-house-fill"></i></button></form>
            <form action="eventos.php" method="get"><button type="submit" class="nav-btn">Eventos</button></form>
            <form action="mis_eventos.php" method="get"><button type="submit" class="nav-btn">Mis Eventos</button></form>
            <form action="detalle_usuario.php" method="get"><button type="submit" class="nav-btn">Detalle de usuario</button></form>
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
    <div style="background:#114c78;min-height:calc(100vh - 100px);padding-top:40px;display:flex;flex-direction:column;align-items:center;">
        <div style="width:80%;max-width:900px;">
        <?php if ($rol_texto === 'admin'): ?>
            <div style="text-align:right; margin-bottom:32px;">
                <button onclick="location.href='agregar_evento.php'" style="background:#5ba4e5;color:#fff;border:none;border-radius:8px;padding:12px 28px;font-size:1.1rem;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.04);">Agregar Evento</button>
            </div>
        <?php endif; ?>
        <?php if ($msg): ?><div style="color:#fff;text-align:center;margin-bottom:18px;font-weight:500;"> <?php echo $msg; ?> </div><?php endif; ?>
        <?php
        // Mostrar eventos de la base de datos
        $eventos = [];
        $res = $conn->query("SELECT id, nombre FROM eventos");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $eventos[] = $row;
            }
        }
        // Si no hay eventos en la base, mostrar mensaje
        if (count($eventos) === 0) {
            echo '<div style="color:#fff;text-align:center;margin-top:32px;font-size:1.2rem;">No hay eventos disponibles.</div>';
        }
        foreach ($eventos as $ev): ?>
            <div style="position:relative;">
                <a href="eventopagina.php?id=<?php echo $ev['id']; ?>" style="text-decoration:none;">
                    <div style="background:#4e7ca7;margin-bottom:24px;border-radius:8px;padding:32px 0;text-align:center;font-size:1.3rem;color:#fff;font-family:'Montserrat',Arial,sans-serif;box-shadow:0 2px 8px rgba(0,0,0,0.04);transition:background 0.2s;cursor:pointer;">
                        <?php echo htmlspecialchars($ev['nombre']); ?>
                    </div>
                </a>
                <?php if ($rol_texto === 'admin' && count($eventos) > 0 && isset($ev['id']) && $res): ?>
                    <form method="get" style="position:absolute;top:12px;right:18px;">
                        <input type="hidden" name="delete" value="<?php echo $ev['id']; ?>">
                        <button type="submit" style="background:none;border:none;color:#fff;font-size:1.5rem;cursor:pointer;" title="Eliminar evento">&#10006;</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
</body>
</html>