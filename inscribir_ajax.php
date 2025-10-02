
<?php
session_start();
require_once 'conexion.php';
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'msg' => 'No autenticado']);
    exit;
}
// Permitir al admin modificar el texto de advertencia
$usuario_id = $_SESSION['usuario_id'];
$evento_id = intval($_POST['evento_id'] ?? 0);
$sql = "SELECT rol FROM usuarios WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$rol = '';
if ($stmt) {
    $stmt->bind_param('i', $usuario_id);
    $stmt->execute();
    $stmt->bind_result($rol);
    $stmt->fetch();
    $stmt->close();
}
// Si el admin envía un nuevo mensaje de advertencia
if ($rol === 'admin' && isset($_POST['advertencia'])) {
    $advertencia = trim($_POST['advertencia']);
    // Guardar el mensaje en la tabla evento_advertencia (crear si no existe)
    $sql = "REPLACE INTO evento_advertencia (evento_id, advertencia) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('is', $evento_id, $advertencia);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'msg' => 'Advertencia actualizada.']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Error al guardar advertencia.']);
    }
    exit;
}
// Proceso normal de inscripción
$sql = "SELECT COUNT(*) FROM inscripciones WHERE usuario_id = ? AND evento_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $usuario_id, $evento_id);
$stmt->execute();
$stmt->bind_result($ya_inscrito);
$stmt->fetch();
$stmt->close();
if ($ya_inscrito) {
    echo json_encode(['success' => false, 'msg' => 'Ya estás inscrito en este evento.']);
    exit;
}
$sql = "INSERT INTO inscripciones (usuario_id, evento_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('ii', $usuario_id, $evento_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'msg' => 'Inscripción exitosa.']);
    } else {
        echo json_encode(['success' => false, 'msg' => 'Error al inscribirse.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'msg' => 'Error en la base de datos.']);
}
