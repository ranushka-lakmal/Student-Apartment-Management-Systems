<?php
session_start();
header('Content-Type: application/json');

require_once 'dbconnection.php'; // Update this path as needed

// Security check: Ensure the user is logged in
if (!isset($_SESSION['login'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch inbox messages with 'is_read' status of '1' from the 'contact' table
$inboxMessages = [];
$query = "SELECT id, name, email, message, DATE_FORMAT(submitted_at, '%Y-%m-%d %H:%i:%s') as submitted_at FROM contact WHERE is_read = 1 ORDER BY submitted_at DESC";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $inboxMessages[] = $row;
}

mysqli_close($con);

echo json_encode($inboxMessages);
?>
