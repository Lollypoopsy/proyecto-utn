<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'conexion.php';
$usuario_id = $_SESSION['usuario_id'];
$rol = '';
$sql = "SELECT rol FROM usuarios WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $stmt->bind_result($rol);
    $stmt->fetch();
    $stmt->close();
}
// Obtener todos los usuarios
$usuarios = [];
$sql = "SELECT nombre, dni, email, condiciones_medicas, estado_pago, localidad, legajo FROM detalle_usuario";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de usuario</title>
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
        .tabla-usuario { background:#4e7ca7;border-radius:8px;padding:24px;margin:40px auto 0 auto;width:80%;max-width:700px;color:#fff; }
        table { width:100%; border-collapse:collapse; margin-top:18px; }
        th, td { padding:10px; border-bottom:1px solid #3571a3; text-align:left; }
        th { background:#3571a3; color:#fff; }
        tr:last-child td { border-bottom:none; }
        .evento-link { color:#fff; text-decoration:none; }
        .evento-link:hover { text-decoration:underline; }
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
                <div style="font-weight:600; font-size:1.1em; margin-bottom:16px; color:#174a6c; text-align:center;">👤 Usuario</div>
            <?php endif; ?>
            <form action="logout.php" method="post">
                <button type="submit" class="nav-btn" style="background:#65bbf8; color:#fff; width:180px;">Cerrar sesión</button>
            </form>
        </div>
    </div>
    <div class="tabla-usuario">
        <h2 style="margin-top:0;">Detalle usuario</h2>
        <table style="background:#8ec6f7;color:#222;border-radius:8px;">
            <tr>
                <th>Nombre y apellido</th>
                <th>DNI</th>
                <th>Email</th>
                <th>Condiciones médicas</th>
                <th>Estado de pago</th>
                <th>Localidad</th>
                <th>Legajo</th>
            </tr>
            <?php if (count($usuarios) === 0): ?>
                <tr><td colspan="7" style="color:#c00;">No hay usuarios registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($u['dni']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($u['condiciones_medicas'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($u['estado_pago'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($u['localidad'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($u['legajo'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>