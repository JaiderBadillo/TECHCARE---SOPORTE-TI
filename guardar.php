<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['ok' => false, 'error' => 'Error de conexión con la base de datos']);
    exit;
}

$nombre        = trim($_POST['nombre'] ?? '');
$email         = trim($_POST['email'] ?? '');
$asunto        = trim($_POST['asunto'] ?? '');
$tipo_problema = trim($_POST['tipo_problema'] ?? 'SOFTWARE');
$prioridad     = trim($_POST['prioridad'] ?? 'media');
$mensaje       = trim($_POST['mensaje'] ?? '');

$tiposValidos = ['RED', 'SOFTWARE', 'HARDWARE', 'SEGURIDAD', 'CLOUD_SERVIDORES', 'BASE_DE_DATOS'];
$prioridadesValidas = ['baja', 'media', 'alta', 'critica'];

if (!$nombre || !$email || !$asunto || !$mensaje) {
    echo json_encode(['ok' => false, 'error' => 'Por favor complete todos los campos obligatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'error' => 'El correo electrónico no es válido.']);
    exit;
}

if (!in_array($tipo_problema, $tiposValidos, true)) {
    $tipo_problema = 'SOFTWARE';
}

if (!in_array($prioridad, $prioridadesValidas, true)) {
    $prioridad = 'media';
}

$stmt = $conn->prepare("INSERT INTO solicitudes (nombre, email, asunto, tipo_problema, prioridad, mensaje, estado) VALUES (?, ?, ?, ?, ?, ?, 'pendiente')");
$stmt->bind_param("ssssss", $nombre, $email, $asunto, $tipo_problema, $prioridad, $mensaje);

if ($stmt->execute()) {
    $nuevoId = $stmt->insert_id;
    echo json_encode([
        'ok' => true,
        'id' => $nuevoId,
        'mensaje' => 'Solicitud de soporte #' . $nuevoId . ' registrada correctamente.'
    ]);
} else {
    echo json_encode(['ok' => false, 'error' => 'No se pudo registrar la solicitud: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
