<?php
session_start();
require_once ('../config/dbconnection.php');

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

$landlordEmail = $_SESSION['login'];
$properties = [];

// get posts
$query = "SELECT id, name, description, price, addr, image1 FROM properties WHERE landlord_email=?";
if ($stmt = mysqli_prepare($con, $query)) {
    mysqli_stmt_bind_param($stmt, 's', $landlordEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $properties[] = $row;
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $propertyId = $_POST['id'];
    $landlordEmail = $_SESSION['login']; // Assuming you're using this to check ownership

    // Prepare the delete query
    $query = "DELETE FROM properties WHERE id = ? AND landlord_email = ?";
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, 'is', $propertyId, $landlordEmail);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
    // Redirect back to the property listing page
    header('Location: view_post.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Properties</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>My Post Listings</h2>
        <div class="table-responsive">
            <table id="propertyTable" class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Address</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): ?>
                        <tr>

                            <td>
                                <?php if (!empty($property['image1'])): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($property['image1']); ?>"
                                        alt="Property Image" style="width: 100px; height: auto;">
                                <?php else: ?>
                                    No image available
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($property['name']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($property['description']); ?>
                            </td>
                            <td>$
                                <?php echo htmlspecialchars($property['price']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($property['addr']); ?>
                            </td>

                            <td>
                                <a href="edit_post.php?id=<?php echo $property['id']; ?>"
                                    class="btn btn-primary btn-sm">Edit</a>
                                <button onclick="confirmDeletion(<?php echo $property['id']; ?>);"
                                    class="btn btn-danger btn-sm">Delete</button>
                            </td>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#propertyTable').DataTable();
        });
    </script>

    <script>
        function confirmDeletion(id) {
            if (confirm('Are you sure you want to delete this property?')) {
                var form = document.createElement('form');
                document.body.appendChild(form);
                form.method = 'post';
                form.action = 'view_post.php';

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = id;
                form.appendChild(input);

                form.submit();
            }
        }
    </script>
</body>

</html>