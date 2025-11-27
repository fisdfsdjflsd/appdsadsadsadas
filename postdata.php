<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Hide errors in production
ini_set('display_errors', 0);
error_reporting(0);

// Database configuration
$db_host = 'sql106.iceiy.com';
$db_user = 'icei_40409346';
$db_pass = 'JQtR5849TjNH';
$db_name = 'icei_40409346_mydb32132132131';

// Connect
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Get JSON input
$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

// Validate required fields
if (!isset($data['current_level']) || !isset($data['target_level']) || !isset($data['avg_delay'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Sanitize & cast
$current_level = floatval($data['current_level']);
$target_level  = floatval($data['target_level']);
$avg_delay     = floatval($data['avg_delay']);

// Insert with timestamp
$stmt = $conn->prepare("INSERT INTO data (current_level, target_level, avg_delay, timestamp) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("ddd", $current_level, $target_level, $avg_delay);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'ok',
        'message' => 'Data saved',
        'id' => $conn->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
}

$stmt->close();
$conn->close();
?>