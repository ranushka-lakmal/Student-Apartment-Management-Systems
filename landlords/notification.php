<?php
session_start();
require_once 'dbconnection.php'; // Update this path as needed

if (!isset($_SESSION['login']) || !isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

// Handle accept/reject action
if (isset($_POST['action']) && isset($_POST['property_id'])) {
    $propertyId = $_POST['property_id'];
    $action = $_POST['action'];
    
    // Set is_reserve based on action
    $isReserve = ($action === 'accept') ? 1 : 0;

    $query = "UPDATE properties SET is_reserve = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ii', $isReserve, $propertyId);
    $stmt->execute();

    // Redirect to refresh the page and show changes
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$landlordEmail = $_SESSION['login'];
$query = "SELECT * FROM properties WHERE landlord_email = ? AND is_reserve = 1";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $landlordEmail);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
<div class="container mt-5">
    <h2>My Notifications</h2>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo $row['is_reserve'] ? 'Reserved' : 'Not Reserved'; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="property_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="accept" class="btn btn-success">Accept</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



