<?php
session_start();
require_once 'conexion.php';

$usuario = [];
if (isset($_SESSION['usuario_id'])) {
    $id = $_SESSION['usuario_id'];
    $sql = "SELECT nombre, telefono, provincia, departamento, localidad, facultad, carrera, condiciones_medicas FROM usuarios WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $nombre = $_POST['nombre'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $provincia = $_POST['provincia'] ?? '';
    $departamento = $_POST['departamento'] ?? '';
    $localidad = $_POST['localidad'] ?? '';
    $facultad = $_POST['facultad'] ?? '';
    $carrera = $_POST['carrera'] ?? '';
    $condiciones = $_POST['condiciones_medicas'] ?? '';
    $id = $_SESSION['usuario_id'];
    $sql = "UPDATE usuarios SET nombre='$nombre', telefono='$telefono', provincia='$provincia', departamento='$departamento', localidad='$localidad', facultad='$facultad', carrera='$carrera', condiciones_medicas='$condiciones' WHERE id='$id'";
    $conn->query($sql);
    // Recargar datos
    $sql = "SELECT nombre, telefono, provincia, departamento, localidad, facultad, carrera, condiciones_medicas FROM usuarios WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    }
    echo '<div id="popup-success" style="position:fixed;top:30px;right:30px;z-index:9999;background:#e6fff0;border-radius:12px;padding:18px 32px;box-shadow:0 2px 12px rgba(0,0,0,0.12);display:flex;align-items:center;gap:12px;font-size:1.2em;color:#1ca85c;font-weight:600;"><span style="font-size:2em;">&#10004;</span> Cambios guardados</div><script>setTimeout(()=>{document.getElementById("popup-success").style.display="none"},1800);</script>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Super QR</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
<!-- Eliminado: bloque <style> y estilos inline. Usar solo global.css y Bootstrap. -->
</head>
<body>
    <div class="header-inicio">
    <div class="nav-btns" style="gap:40px;">
            <form action="inicio.php" method="get"><button type="submit" class="nav-btn nav-btn-home d-flex align-items-center justify-content-center"><i class="bi bi-house-fill"></i></button></form>
            <form action="eventos.php" method="get"><button type="submit" class="nav-btn">Eventos</button></form>
            <form action="misdatos.php" method="get"><button type="submit" class="nav-btn">Mis Datos</button></form>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <form action="detalle_usuario.php" method="get"><button type="submit" class="nav-btn">Detalle de usuario</button></form>
            <?php endif; ?>
            <form action="qr.php" method="get"><button type="submit" class="nav-btn">QR</button></form>
        </div>
        <span class="user-icon" id="userIcon"><i class="bi bi-person-circle"></i></span>
        <div class="user-menu" id="userMenu" style="display:none; position:absolute; top:90px; right:40px; background:#fff; color:#222; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.15); padding:24px 32px; z-index:9999;">
            <?php if (!empty($usuario['nombre'])): ?>
                <div class="user-menu-title">👤 <?php echo htmlspecialchars($usuario['nombre']); ?></div>
            <?php endif; ?>
            <form action="logout.php" method="post">
                <button type="submit" class="nav-btn nav-btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </div>
    <div class="main">
        <form method="post">
        <div class="datos-box">
            <div class="datos-col">
                <label class="datos-label">Nombre</label>
                <input type="text" name="nombre" class="datos-input" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>">
                <label class="datos-label">Telefono</label>
                <input type="text" name="telefono" class="datos-input" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                <label class="datos-label">Provincia</label>
                <select id="provincia" name="provincia" class="datos-input" style="background:#65bbf8; color:#fff;"></select>
                <label class="datos-label">Departamento</label>
                <select id="departamento" name="departamento" class="datos-input" style="background:#65bbf8; color:#fff;"></select>
                <label class="datos-label">Localidad</label>
                <select id="localidad" name="localidad" class="datos-input" style="background:#65bbf8; color:#fff;"></select>
            </div>
            <div class="datos-col">
                <label class="datos-label">Facultad</label>
                <input type="text" name="facultad" class="datos-input" value="<?php echo htmlspecialchars($usuario['facultad'] ?? ''); ?>">
                <label class="datos-label">Carrera</label>
                <input type="text" name="carrera" class="datos-input" value="<?php echo htmlspecialchars($usuario['carrera'] ?? ''); ?>">
                <label class="datos-label">Condiciones médicas o alimenticias</label>
                <input type="text" name="condiciones_medicas" class="datos-input" value="<?php echo htmlspecialchars($usuario['condiciones_medicas'] ?? ''); ?>">
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <button type="submit" class="nav-btn" style="background:#65bbf8; color:#fff; font-size:1.1em; width:auto; min-width:0; padding:10px 24px;">Guardar cambios</button>
        </div>
        </form>
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

        // Obtener valores guardados desde PHP
        const provinciaGuardada = <?php echo json_encode($usuario['provincia'] ?? ''); ?>;
        const departamentoGuardado = <?php echo json_encode($usuario['departamento'] ?? ''); ?>;
        const localidadGuardada = <?php echo json_encode($usuario['localidad'] ?? ''); ?>;

        // Cargar provincias y preseleccionar
        fetch('https://apis.datos.gob.ar/georef/api/provincias')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('provincia');
                select.innerHTML = '<option value="">Seleccione provincia</option>';
                data.provincias.forEach(p => {
                    select.innerHTML += `<option value="${p.nombre}"${provinciaGuardada === p.nombre ? ' selected' : ''}>${p.nombre}</option>`;
                });
                if (provinciaGuardada) {
                    cargarDepartamentos(provinciaGuardada);
                }
            });

        function cargarDepartamentos(prov) {
            const depSelect = document.getElementById('departamento');
            depSelect.innerHTML = '<option value="">Cargando...</option>';
            fetch(`https://apis.datos.gob.ar/georef/api/departamentos?provincia=${encodeURIComponent(prov)}&campos=id,nombre&max=1000`)
                .then(res => res.json())
                .then(data => {
                    depSelect.innerHTML = '<option value="">Seleccione departamento</option>';
                    data.departamentos.forEach(d => {
                        depSelect.innerHTML += `<option value="${d.nombre}"${departamentoGuardado === d.nombre ? ' selected' : ''}>${d.nombre}</option>`;
                    });
                    if (departamentoGuardado) {
                        cargarLocalidades(prov, departamentoGuardado);
                    }
                });
        }

        function cargarLocalidades(prov, dep) {
            const locSelect = document.getElementById('localidad');
            locSelect.innerHTML = '<option value="">Cargando...</option>';
            fetch(`https://apis.datos.gob.ar/georef/api/localidades?provincia=${encodeURIComponent(prov)}&departamento=${encodeURIComponent(dep)}&campos=id,nombre&max=1000`)
                .then(res => res.json())
                .then(data => {
                    locSelect.innerHTML = '<option value="">Seleccione localidad</option>';
                    data.localidades.forEach(l => {
                        locSelect.innerHTML += `<option value="${l.nombre}"${localidadGuardada === l.nombre ? ' selected' : ''}>${l.nombre}</option>`;
                    });
                });
        }

        document.getElementById('provincia').addEventListener('change', function() {
            cargarDepartamentos(this.value);
            document.getElementById('localidad').innerHTML = '<option value="">Seleccione localidad</option>';
        });

        document.getElementById('departamento').addEventListener('change', function() {
            const prov = document.getElementById('provincia').value;
            cargarLocalidades(prov, this.value);
        });
        </script>
    </div>
</body>
</html>
