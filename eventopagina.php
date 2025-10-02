    <!-- Estilos trasladados a global.css -->
<?php
// eventopagina.php - Detalle de evento
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
// Obtener el id del evento

$evento_id = intval($_GET['id'] ?? 1);
$msg = '';
// Actualizar datos si el admin envía el formulario
// Inscribir usuario al evento
if (isset($_POST['inscribirme']) && $_POST['inscribirme'] == '1') {
    // Verificar si ya está inscrito
    $sql = "SELECT COUNT(*) FROM inscripciones WHERE usuario_id = ? AND evento_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $usuario_id, $evento_id);
    $stmt->execute();
    $stmt->bind_result($ya_inscrito);
    $stmt->fetch();
    $stmt->close();
    if (!$ya_inscrito) {
        $sql = "INSERT INTO inscripciones (usuario_id, evento_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $usuario_id, $evento_id);
            if ($stmt->execute()) {
                $msg = 'Inscripción exitosa.';
            } else {
                $msg = 'Error al inscribirse.';
            }
            $stmt->close();
        }
    } else {
        $msg = 'Ya estás inscrito en este evento.';
    }
}
if ($rol === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $campo = $_POST['campo'] ?? '';
    $valor = trim($_POST['valor'] ?? '');
    $update = false;
    if ($campo && in_array($campo, ['nombre','descripcion','imagen','lugar','fecha'])) {
        $sql = "UPDATE eventos SET $campo=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('si', $valor, $evento_id);
            if ($stmt->execute()) {
                $msg = ucfirst($campo) . ' actualizado correctamente.';
            } else {
                $msg = 'Error al actualizar ' . $campo;
            }
            $stmt->close();
        }
    } elseif (isset($_POST['guardar_todo'])) {
        $nuevo_titulo = trim($_POST['titulo'] ?? '');
        $nueva_descripcion = trim($_POST['descripcion'] ?? '');
        $nueva_imagen = trim($_POST['imagen'] ?? '');
        $nuevo_lugar = trim($_POST['lugar'] ?? '');
        $nueva_fecha = trim($_POST['fecha'] ?? '');
        $sql = "UPDATE eventos SET nombre=?, descripcion=?, imagen=?, lugar=?, fecha=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('sssssi', $nuevo_titulo, $nueva_descripcion, $nueva_imagen, $nuevo_lugar, $nueva_fecha, $evento_id);
            if ($stmt->execute()) {
                $msg = 'Evento actualizado correctamente.';
            } else {
                $msg = 'Error al actualizar el evento.';
            }
            $stmt->close();
        }
    }
}
// Obtener datos del evento desde la base
$sql = "SELECT nombre, descripcion, imagen, lugar, fecha FROM eventos WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $evento_id);
    $stmt->execute();
    $stmt->bind_result($titulo, $descripcion, $imagen, $lugar, $fecha);
    if (!$stmt->fetch()) {
        $titulo = 'Evento no encontrado';
        $descripcion = '';
        $imagen = '';
        $lugar = '';
        $fecha = '';
    }
    $stmt->close();
}
// Valores por defecto si están vacíos
$titulo = (isset($titulo) && $titulo !== '') ? $titulo : 'agregar titulo';
$descripcion = (isset($descripcion) && $descripcion !== '') ? $descripcion : 'agregar descripcion';
$imagen = (isset($imagen) && $imagen !== '') ? $imagen : 'agregar imagen';
$lugar = (isset($lugar) && $lugar !== '') ? $lugar : 'agregar lugar'; 
$fecha = (isset($fecha) && $fecha !== '') ? $fecha : 'agregar fecha';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Evento</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
    <div class="header-inicio">
        <div class="nav-btns d-flex align-items-center flex-row gap-3">
            <form action="inicio.php" method="get" class="m-0"><button type="submit" class="nav-btn nav-btn-home d-flex align-items-center justify-content-center"><i class="bi bi-house-fill"></i></button></form>
            <form action="eventos.php" method="get" class="m-0"><button type="submit" class="nav-btn">Eventos</button></form>
            <form action="mis_eventos.php" method="get" class="m-0"><button type="submit" class="nav-btn">Mis Eventos</button></form>
        </div>
        <span class="user-icon" id="userIcon"><i class="bi bi-person-circle"></i></span>
        <div class="user-menu" id="userMenu">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <div class="user-menu-title">👤 <?php echo htmlspecialchars($nombre); ?></div>
            <?php endif; ?>
            <form action="logout.php" method="post">
                <button type="submit" class="nav-btn nav-btn-logout">Cerrar sesión</button>
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
    <div class="main-title"><?php echo htmlspecialchars($titulo); ?></div>
    <?php if ($msg): ?><div class="evento-msg"> <?php echo $msg; ?> </div><?php endif; ?>
    <div class="evento-content">
        <form id="eventoForm" method="post" class="evento-form">
            <div class="evento-desc">
                <div class="evento-titulo">
                    <input type="text" name="titulo" id="tituloInput" value="<?php echo ($titulo !== 'agregar titulo') ? htmlspecialchars($titulo) : ''; ?>" placeholder="agregar titulo" class="evento-titulo-input" readonly>
                </div>
                <div class="evento-descripcion">
                    <textarea name="descripcion" id="descripcionInput" rows="7" placeholder="agregar descripcion" class="evento-desc-textarea" readonly><?php echo ($descripcion !== 'agregar descripcion') ? htmlspecialchars($descripcion) : ''; ?></textarea>
                </div>
            </div>
            <div class="evento-img">
                <div class="evento-img-url">
                    <input type="url" name="imagen" id="imagenInput" value="<?php echo ($imagen !== 'agregar imagen') ? htmlspecialchars($imagen) : ''; ?>" placeholder="agregar imagen" class="evento-img-input" pattern="https?://.+" readonly>
                </div>
                <?php
                if ($imagen && $imagen !== 'agregar imagen') {
                    if (filter_var($imagen, FILTER_VALIDATE_URL)) {
                        echo '<img src="' . htmlspecialchars($imagen) . '" alt="Imagen evento" class="evento-img-preview">';
                    } else {
                        echo '<div class="evento-img-error">URL de imagen inválida</div>';
                    }
                }
                ?>
                <div class="evento-img-lugar">
                    <input type="text" name="lugar" id="lugarInput" value="<?php echo ($lugar !== 'agregar lugar') ? htmlspecialchars($lugar) : ''; ?>" placeholder="agregar lugar" class="evento-img-lugar-input" readonly>
                </div>
                <div class="evento-img-fecha">
                    <input type="text" name="fecha" id="fechaInput" value="<?php echo ($fecha !== 'agregar fecha') ? htmlspecialchars($fecha) : ''; ?>" placeholder="agregar fecha" class="evento-img-fecha-input" readonly>
                </div>
            </div>
        </form>
    </div>
    <?php if ($rol === 'admin'): ?>
        <div class="evento-admin-btns">
            <button type="button" id="modificarBtn" class="ins-btn w-auto d-inline-block">Modificar</button>
            <button type="submit" form="eventoForm" id="guardarBtn" class="ins-btn evento-guardar-btn">Guardar</button>
        </div>
    <?php endif; ?>
    <!-- Modal de inscripción -->
    <div id="modalInscripcion" class="modal-inscripcion">
      <div class="modal-inscripcion-content">
        <h2>Inscripción</h2>
        <div class="modal-inscripcion-row">
          <label>Alias:</label>
          <div class="modal-inscripcion-alias">UTNcongreso</div>
        </div>
        <div class="modal-inscripcion-title">Enviar comprobante:</div>
        <div class="modal-inscripcion-row">
          <label>Telefono</label>
          <div class="modal-inscripcion-telefono">+54 9 3564 555555</div>
        </div>
        <div class="modal-inscripcion-row">
          <label>Email</label>
          <div class="modal-inscripcion-email">utn@gmail.com</div>
        </div>
        <div id="msgInscripcion" class="modal-inscripcion-msg"></div>
        <button id="btnConfirmarInscripcion" class="ins-btn modal-inscripcion-confirmar">Confirmar inscripción</button>
        <button onclick="cerrarModalInscripcion()" class="ins-btn modal-inscripcion-cancelar">Cancelar</button>
      </div>
    </div>
            <div class="evento-inscribirme-btn text-center mt-4">
                <a href="#" class="ins-btn w-auto d-inline-block">Inscribirme</a>
            </div>
</body>
</html>