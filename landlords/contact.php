<?php
// Start the session to display messages
session_start();

require_once 'dbconnection.php';

$fname = '';
$email = '';
if(isset($_SESSION['login'])) {
    // Assuming $_SESSION['login'] contains the user's email
    $userEmail = $_SESSION['login'];

    // Prepare and execute query to fetch user details
    $query = "SELECT fname, email FROM users WHERE email = ?";
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $stmt->bind_result($dbFname, $dbEmail);
        if ($stmt->fetch()) {
            // Set fetched details to variables
            $fname = $dbFname;
            $email = $dbEmail;
        }
        $stmt->close();
    }
}

// Check for a success message in the session data
$successMessage = '';
if (isset($_SESSION['message_status'])) {
    $successMessage = $_SESSION['message_status'];
    unset($_SESSION['message_status']); // Clear the message
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Contact Admin</h5>
                    <form action="contact_process.php" method="POST">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($fname); ?>" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>


<!-- SweetAlert Success Message -->
<?php if (!empty($successMessage)): ?>
<script>
    swal({
        title: "Success!",
        text: "<?php echo $successMessage; ?>",
        type: "success",
        confirmButtonText: "OK"
    });
</script>
<?php 
// Clear the success message from session
unset($_SESSION['message_status']);
endif; 
?>
 
</body>
</html>
