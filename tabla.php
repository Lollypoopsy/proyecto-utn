<?php
session_start();
require_once 'conexion.php';
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: inicio.php');
    exit;
}
// Eliminar usuario escaneado si se solicita
if (isset($_POST['eliminar']) && isset($_POST['indice'])) {
    $indice = (int)$_POST['indice'];
    if (isset($_SESSION['qr_codes'][$indice])) {
        array_splice($_SESSION['qr_codes'], $indice, 1);
    }
    header('Location: tabla.php');
    exit;
}
// Guardar el QR escaneado en la sesión
if (isset($_GET['qr'])) {
    $qr = $_GET['qr'];
    // Si el QR es una URL con uid, extraer el valor
    if (preg_match('/[?&]uid=(\d+)/', $qr, $matches)) {
        $id = $matches[1];
    } elseif (preg_match('/^\d+$/', $qr)) {
        $id = $qr;
    } else {
        $id = null;
    }
    if ($id !== null) {
        if (!isset($_SESSION['qr_codes'])) {
            $_SESSION['qr_codes'] = [];
        }
        $_SESSION['qr_codes'][] = $id;
    }
}
$qr_codes = isset($_SESSION['qr_codes']) ? $_SESSION['qr_codes'] : [];

// Obtener datos de usuarios escaneados
$usuarios = [];
if (count($qr_codes) > 0) {
    // Evitar duplicados
    $ids = array_unique($qr_codes);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $sql = "SELECT id, nombre, telefono, facultad, provincia, localidad, carrera, condiciones_medicas FROM usuarios WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);   
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $usuarios[$row['id']] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tabla de QR Escaneados</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #174a6c; color: #fff; font-family: 'Montserrat', Arial, sans-serif; }
        .container { background: rgba(255,255,255,0.07); border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.12); padding: 40px 32px; max-width: 600px; margin: 60px auto; text-align: center; }
        table { background: #fff; color: #174a6c; border-radius: 8px; width: 100%; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; }
        th { background: #4876a5ff; color: #fff; }
        .nav-btns { display: flex; gap: 40px; margin-bottom: 30px; justify-content: center; }
        .nav-btn { background: #174a6c; color: #fff; border: none; border-radius: 7px; font-size: 1em; font-weight: 500; padding: 10px 32px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: background 0.2s; text-decoration: none; }
        .nav-btn:hover { background: #205b85; }
    </style>
</head>
<body>
    <div class="container" style="overflow-x:auto; max-width:98vw; min-width:0;">
        <div class="nav-btns">
            <a href="inicio.php" class="nav-btn">Inicio</a>
            <a href="qr.php" class="nav-btn">QR</a>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <a href="escanear_qr.php" class="nav-btn">Scanner</a>
                <a href="tabla.php" class="nav-btn">Tabla</a>
            <?php endif; ?>
        </div>
        <h1>Usuarios escaneados</h1>
        <div style="width:100%; overflow-x:auto;">
            <table class="table table-bordered table-responsive" style="min-width:900px;">
                <thead>
                    <tr><th>#</th><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Facultad</th><th>Provincia</th><th>Localidad</th><th>Carrera</th><th>Cond. Médica</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                <?php if (count($qr_codes) === 0): ?>
                    <tr><td colspan="10">No hay códigos QR escaneados.</td></tr>
                <?php else: ?>
                    <?php foreach ($qr_codes as $i => $qr_id): ?>
                        <?php if (isset($usuarios[$qr_id])): $u = $usuarios[$qr_id]; ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($u['id']); ?></td>
                                <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($u['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($u['facultad']); ?></td>
                                <td><?php echo htmlspecialchars($u['provincia']); ?></td>
                                <td><?php echo htmlspecialchars($u['localidad']); ?></td>
                                <td><?php echo htmlspecialchars($u['carrera']); ?></td>
                                <td><?php echo htmlspecialchars($u['condiciones_medicas']); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="indice" value="<?php echo $i; ?>">
                                        <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td colspan="9">ID <?php echo htmlspecialchars($qr_id); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>