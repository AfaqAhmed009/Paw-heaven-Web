<?php
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Please login to manage favorites']);
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$petId = isset($_POST['pet_id']) ? intval($_POST['pet_id']) : 0;

if ($action !== 'toggle' || $petId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}

$conn = getDBConnection();

// Check if already favorited
$checkStmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND pet_id = ?");
$checkStmt->bind_param("ii", $userId, $petId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    // Remove from favorites
    $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND pet_id = ?");
    $stmt->bind_param("ii", $userId, $petId);
    $stmt->execute();
    echo json_encode(['success' => true, 'favorited' => false]);
} else {
    // Add to favorites
    $stmt = $conn->prepare("INSERT INTO favorites (user_id, pet_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $petId);
    $stmt->execute();
    echo json_encode(['success' => true, 'favorited' => true]);
}

$stmt->close();
$checkStmt->close();
$conn->close();
