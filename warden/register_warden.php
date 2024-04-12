<?php
session_start();
require_once 'dbconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $fname = $con->real_escape_string($_POST['fname']);
    $lname = $con->real_escape_string($_POST['lname']);
    $email = $con->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $contactno = $con->real_escape_string($_POST['contactno']);
    $usr_typ = 'warden';
    $stat_code = 1;

    // Check if email already exists
    $checkEmail = $con->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();
    if ($checkEmail->num_rows > 0) {
        $message = "Email already exists!";
        echo "<script>alert('$message'); window.location='register.php';</script>";
    } else {
        $stmt = $con->prepare("INSERT INTO users (fname, lname, email, password, contactno, usr_typ, stat_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssi', $fname, $lname, $email, $password, $contactno, $usr_typ, $stat_code);

        if ($stmt->execute()) {
            echo "<script>alert('Warden registered successfully!'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error occurred while registering. Please try again.'); window.location='register.php';</script>";
        }
        $stmt->close();
    }
    $con->close();
}
?>
