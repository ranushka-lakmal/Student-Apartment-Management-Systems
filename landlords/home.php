<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background-color: #007bff;
            color: white;
            padding: 100px 0;
        }

        .features-section {
            padding: 50px 0;
        }

        .footer {
            background-color: #333;
            color: white;
            padding: 30px 0;
            text-align: center;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="hero-section text-center">
    <h1>Welcome to LandlordPortal</h1>
    <p>Manage your properties efficiently and with ease.</p>
</div>

<div class="container features-section">
    <div class="row">
        <div class="col-md-4">
            <h2>Feature 1</h2>
            <p>Description of a key feature or benefit for landlords using your platform.</p>
        </div>
        <div class="col-md-4">
            <h2>Feature 2</h2>
            <p>Another important benefit or tool that makes landlords' lives easier.</p>
        </div>
        <div class="col-md-4">
            <h2>Feature 3</h2>
            <p>Highlight the ease of property management, reporting, or financial tracking.</p>
        </div>
    </div>
</div>

<div class="footer">
    <p>LandlordPortal &copy; <?php echo date("Y"); ?></p>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


