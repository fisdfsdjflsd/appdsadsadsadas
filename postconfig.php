<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 0);
error_reporting(0);

// Database
$db_host = 'sql106.iceiy.com';
$db_user = 'icei_40409346';
$db_pass = 'JQtR5849TjNH';
$db_name = 'icei_40409346_mydb32132132131';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
    exit;
}

$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

if (!isset($data['kp']) || !isset($data['ki']) || !isset($data['kd']) || !isset($data['t_lag'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
    exit;
}

$kp     = floatval($data['kp']);
$ki     = floatval($data['ki']);
$kd     = floatval($data['kd']);
$t_lag  = intval($data['t_lag']);

$stmt = $conn->prepare("INSERT INTO configs (kp, ki, kd, t_lag, timestamp) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("dddi", $kp, $ki, $kd, $t_lag);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'ok',
        'message' => 'Config saved',
        'id' => $conn->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
}

$stmt->close();
$conn->close();
?>