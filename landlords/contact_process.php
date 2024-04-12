<?php
session_start();
require_once '../config/dbconnection.php'; // Adjust the path as needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
   

    // Insert the message into the database
    $query = "INSERT INTO contact (name, email, message, is_read) VALUES (?, ?, ?, 1)";
    
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, 'sss', $name, $email, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message_status'] = 'Thank you for contacting us. We will be in touch shortly.';
        } else {
            $_SESSION['message_status'] = 'There was a problem with your submission, please try again.';
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message_status'] = 'Failed to prepare the statement.';
    }
    
    mysqli_close($con);
    
    // Redirect back to the contact page
    header('Location: contact.php');
    exit;
}
?>
