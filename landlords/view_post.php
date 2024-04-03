<?php
session_start();
require_once ('../config/dbconnection.php');

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

$landlordEmail = $_SESSION['login'];
$properties = [];

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
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css"
        type="text/css">

    <style>

body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
    }


        .table-container {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        }

        table {
            border-collapse: collapse;
            width: max-content;
            min-width: 100%;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            text-align: left;
            padding: 12px 15px;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
        background-color: #ddd;
    }

        .edit-btn,
        .delete-btn {
            text-decoration: none;
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #007bff;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        @media (max-width: 600px) {

            .edit-btn,
            .delete-btn {
                padding: 3px 6px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>


    <div class="container mt-5">
    <h2>My Property Listings</h2>
    <div class="table-container">
       
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Address</th>
                    <th>Image</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($properties as $property): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($property['id']); ?>
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
                            <?php
                            // Check if the image data is not empty
                            if (!empty($property['image1'])) {
                                // Convert the binary data to a base64 encoded string
                                $imageData = base64_encode($property['image1']);
                                // Create the data URL
                                $src = 'data:image/jpeg;base64,' . $imageData;
                                echo '<img src="' . $src . '" alt="Property Image" style="width: 100px; height: auto;">';
                            } else {
                                echo 'No image available';
                            }
                            ?>
                        </td>

                        <td>
                            <button class="edit-btn"
                                onclick="location.href='edit_post.php?id=<?php echo $property['id']; ?>'">Edit</button>
                            <button class="delete-btn"
                                onclick="location.href='delete_post.php?id=<?php echo $property['id']; ?>'">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($properties)): ?>
                    <tr>
                        <td colspan="7">You have not listed any properties yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
   

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>