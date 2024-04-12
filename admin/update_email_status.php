<?php
session_start();
require_once 'dbconnection.php'; // Ensure this is pointing to your actual database connection script

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id'])) {
    echo json_encode(['error' => 'No email ID provided']);
    exit;
}

$id = $input['id'];

// Update is_read status to 0
$query = "UPDATE contact SET is_read = 0 WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $id);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => 'Email status updated.']);
} else {
    echo json_encode(['error' => 'Failed to update email status.']);
}

$stmt->close();
$con->close();
?>
