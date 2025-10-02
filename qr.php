<?php
session_start();
require_once 'conexion.php';
require_once 'phpqrcode/qrlib.php'; // Incluye la librería QR

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT nombre, email FROM usuarios WHERE id = $usuario_id";
$result = $conn->query($sql);

$rol = '';
$sql_rol = "SELECT rol FROM usuarios WHERE id = $usuario_id";
$result_rol = $conn->query($sql_rol);
if ($result_rol && $result_rol->num_rows > 0) {
    $rol = $result_rol->fetch_assoc()['rol'];
}

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Obtener el rol y guardarlo en la sesión
    if (!isset($_SESSION['rol'])) {
        $sql_rol = "SELECT rol FROM usuarios WHERE id = $usuario_id";
        $result_rol = $conn->query($sql_rol);
        if ($result_rol && $result_rol->num_rows > 0) {
            $_SESSION['rol'] = $result_rol->fetch_assoc()['rol'];
        }
    }
    // URL para registrar evento, reemplaza 'localhost/UTNprojects-main' por tu dominio si es necesario
        $qr_data = "http://localhost/proyecto_utn/mostrar_datos.php?uid={$usuario_id}";
    // Genera el QR en memoria y lo muestra como imagen
    ob_start();
    QRcode::png($qr_data, null, QR_ECLEVEL_L, 6);
    $imageString = base64_encode(ob_get_contents());
    ob_end_clean();
} else {
    $qr_data = '';
    $imageString = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi QR UTN</title>
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
            text-decoration: none; /* Eliminar subrayado en enlaces */
        }
        .nav-btn:hover {
            background: #205b85;
        }
        .container { background: rgba(255,255,255,0.07); border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.12); padding: 40px 32px; max-width: 370px; margin: 60px auto; text-align: center; }
        h1 { font-size: 2.2rem; margin-bottom: 0.5rem; }
        .codigo { font-size: 1.2rem; background: #fff; color: #165684; padding: 12px 18px; border-radius: 8px; margin: 18px 0; display: inline-block; letter-spacing: 2px; font-weight: bold; }
        .logo-bar { width: 220px; background: #7da6c7; position: fixed; right: 0; top: 0; bottom: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .logo-utn { writing-mode: vertical-rl; text-align: center; font-size: 2.2rem; font-weight: bold; letter-spacing: 2px; margin-top: 40px; }
        .logo-utn span { font-size: 1.1rem; font-weight: normal; }
        @media (max-width: 700px) { .logo-bar { width: 100%; min-height: 120px; position: static; } .logo-utn { writing-mode: horizontal-tb; font-size: 1.3rem; margin-top: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav-btns">
            <form action="inicio.php" method="get" class="m-0"><button type="submit" class="nav-btn nav-btn-home d-flex align-items-center justify-content-center"><i class="bi bi-house-fill"></i></button></form>
            <form action="eventos.php" method="get"><button type="submit" class="nav-btn">Eventos</button></form>
            <form action="misdatos.php" method="get"><button type="submit" class="nav-btn">Mis Datos</button></form>
            <form action="qr.php" method="get"><button type="submit" class="nav-btn">QR</button></form>
            <?php if ($rol === 'admin'): ?>
                <form action="escanear_qr.php" method="get"><button type="submit" class="nav-btn">Scanner</button></form>
                <form action="tabla.php" method="get"><button type="submit" class="nav-btn">Tabla</button></form>
            <?php endif; ?>
        </div>
        <span class="user-icon" id="userIcon"><i class="bi bi-person-circle"></i></span>
        <div class="user-menu" id="userMenu" style="display:none; position:absolute; top:90px; right:40px; background:#fff; color:#222; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.15); padding:24px 32px; z-index:9999;">
            <?php if (!empty($user['nombre'])): ?>
                <div style="font-weight:600; font-size:1.1em; margin-bottom:16px; color:#174a6c; text-align:center;">👤 <?php echo htmlspecialchars($user['nombre']); ?></div>
            <?php endif; ?>
            <form action="logout.php" method="post">
                <button type="submit" class="nav-btn" style="background:#65bbf8; color:#fff; width:180px;">Cerrar sesión</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('userIcon').onclick = function(event) {
            event.stopPropagation();
            var menu = document.getElementById('userMenu');
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
        };
        document.addEventListener('click', function(e) {
            var menu = document.getElementById('userMenu');
            var icon = document.getElementById('userIcon');
            if (menu.style.display === 'block' && !menu.contains(e.target) && e.target !== icon && !icon.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    </script>
    <div class="container">
        <h1>Código QR UTN</h1>
        <?php if ($imageString): ?>
            <img id="qr-img" src="data:image/png;base64,<?php echo $imageString; ?>" alt="QR Usuario" style="margin:18px 0;">
            <br>
            <a id="download-qr" href="data:image/png;base64,<?php echo $imageString; ?>" download="qr-utn.png" class="btn btn-success mt-2">
                <i class="bi bi-download"></i> Descargar QR
            </a>
        <?php else: ?>
            <div class="codigo">No se pudo generar el QR</div>
        <?php endif; ?> 
    </div>
    <div class="logo-bar">
        <div class="logo-utn">
            UNIVERSIDAD TECNOLÓGICA NACIONAL<br><br><span></span>
        </div>
    </div>
</body>
</html>