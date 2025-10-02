<?php
// guardar_qr.php
include 'conexion.php';
$data = json_decode(file_get_contents('php://input'), true);
$qr = $data['qr'] ?? '';

if ($qr) {
    $stmt = $conn->prepare("INSERT INTO tabla_qr (dato) VALUES (?)");
    $stmt->bind_param('s', $qr);
    $ok = $stmt->execute();
    echo json_encode(['success' => $ok]);
} else {
    echo json_encode(['success' => false]);
}
?>