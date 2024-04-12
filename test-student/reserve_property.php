<?php
session_start();
include 'dbconnection.php';

if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['property_id'])) {
    $propertyId = intval($_POST['property_id']);

    $query = "UPDATE properties SET is_reserve = 1 WHERE id = ?";
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $propertyId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($con);

// Redirect back to the property-details page or wherever you like
header('Location: property-detail_test.php?id=' . $propertyId);
exit;
?>
