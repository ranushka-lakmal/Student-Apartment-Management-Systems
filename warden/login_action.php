<?php
session_start();
require_once 'dbconnection.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $con->real_escape_string($_POST['email']);
    $password = $_POST['password']; // Do not escape passwords

    // Check if email exists and get user data
    $stmt = $con->prepare("SELECT id, email, password, usr_typ, stat_code FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password and user type
        if (password_verify($password, $user['password'])) {
            if ($user['usr_typ'] === 'warden' && $user['stat_code'] == 0) {
                $_SESSION['user_id'] = $user['id'];
                $response['success'] = true;
            } else {
                $response['message'] = 'Unauthorized access. Contact admin and approve.!';
            }
        } else {
            $response['message'] = 'Incorrect password.';
        }
    } else {
        $response['message'] = 'Email does not exist.';
    }
    $stmt->close();
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
