<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexión con la base de datos']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$estado = trim($_POST['estado'] ?? '');

$estadosValidos = ['pendiente', 'en_proceso', 'resuelto'];
if ($id <= 0 || !in_array($estado, $estadosValidos, true)) {
    echo json_encode(['ok' => false, 'error' => 'Parámetros inválidos']);
    exit;
}

$stmt = $conn->prepare("UPDATE solicitudes SET estado = ? WHERE id = ?");
$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado exitosamente']);
} else {
    echo json_encode(['ok' => false, 'error' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
