<?php
session_start();
require_once 'dbconnection.php'; // Update this path as needed

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_status'], $_POST['property_id'])) {
    $newStatus = $_POST['change_status'] === 'approve' ? 0 : 9;
    $propertyId = $_POST['property_id'];
    if (changePropertyStatus($propertyId, $newStatus)) {
        // Status changed
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page to reflect changes
        exit;
    } else {
        // Handle error
        $error = "Failed to change the status.";
    }
}



// Function to change the status of the property
function changePropertyStatus($propertyId, $status)
{
    global $con; // Use the connection from the global scope
    $query = "UPDATE properties SET stat_code=? WHERE id=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ii', $status, $propertyId);
    return $stmt->execute();
}

// Check for status change requests
if (isset($_POST['change_status'])) {
    $newStatus = $_POST['change_status'] === 'approve' ? 0 : 9;
    changePropertyStatus($_POST['property_id'], $newStatus);
}

function getStatusText($code)
{
    switch ($code) {
        case 0:
            return 'Approved';
        case 1:
            return 'Pending';
        case 9:
            return 'Rejected';
        default:
            return 'Unknown'; // Default case
    }
}

// Fetch properties data
$query = "SELECT id, name, description, price, addr, stat_code, image1,image2, image3, image4, image5 FROM properties";
$result = mysqli_query($con, $query);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Wordern</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <style>
        body {

            background-color: #f8f9fa;
            /* Light gray background */
        }

        .table {
            color: #212529;
            border-collapse: separate;
            border-spacing: 0 0.5em;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr {
            background-color: #ffffff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            /* Slight shadow on each table row */
        }

        .table tbody tr td {
            padding: .75rem;
            vertical-align: middle;
        }

        .table tbody tr:first-child td {
            border-top: 0;
        }

        /* Add padding and some styling to header */
        .main-content {
            padding: 2rem;
            background: #fff;
            /* White background for content */
            border-radius: 0.5rem;
            /* Rounded corners */
            margin-top: 2rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            /* Slight shadow to content */
        }

        /* More styles */
        .header {
            padding: 1rem 0;
            border-bottom: 2px solid #dee2e6;
        }

        .header h2 {
            margin: 0;
        }

        .navbar {
            margin-bottom: 2rem;
            /* Give some space below the navbar */
        }

        /* Optional: Use a custom font */
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap');

        body {
            font-family: 'Open Sans', sans-serif;
        }

        .info-box {
            color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .blue-bg {
            background: #3498db;
        }

        .header-bg {
            background: #36454F;
        }

        /* You should create additional classes for different colors as shown in the image */

        .count {
            display: block;
            font-size: 20px;
            font-weight: bold;
        }

        .title {
            font-size: 16px;
        }

        #wrapper {
            padding: 20px;
        }

        .info-box {
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 15px;
            color: #fff;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .orange-bg {
            background: #FFC300;
            box-shadow: 0 8px 15px rgba(52, 152, 219, 0.3);
        }

        .blue-bg {
            background: #3498db;
            box-shadow: 0 8px 15px rgba(52, 152, 219, 0.3);
        }

        .info-box .fa {
            font-size: 60px;
            margin-bottom: 10px;
        }

        .info-box .count {
            display: block;
            font-size: 30px;
            font-weight: 600;
        }

        .info-box .title {
            font-size: 18px;
            font-weight: 400;
        }

        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(52, 152, 219, 0.5);
        }

        .carousel-inner>.item>img {
            margin: auto;
            /* Center the image */
            width: 250px;
            /* Fixed width */
            height: 250px;
            /* Fixed height */
            object-fit: cover;
            /* Scale the image to cover the area without losing aspect ratio */
        }

        @media (max-width: 768px) {
            .info-box {
                margin-bottom: 10px;
            }

            .info-box .fa {
                font-size: 40px;
            }

            .info-box .count {
                font-size: 24px;
            }

            .info-box .title {
                font-size: 16px;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid green;
        }

        th {
            background-color: #f2f2f2;
        }

        .button {
            padding: 5px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin: 4px 2px;
            cursor: pointer;
        }


        .stylish-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;

            border: none;
            border-radius: 25px;

            color: #fff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-decoration: none;
        }

        .stylish-button-approve {
            background-color: #28a745;
        }

        .stylish-button-approve:hover {
            background-color: #218838;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .stylish-button-reject {
            background-color: #dc3545;
        }

        .stylish-button-reject:hover {
            background-color: #c82333;
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
    </style>
</head>

<body>


<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0" href="index.php">WELCOME</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Spacer div to push the nav items to the right -->
    <div class="navbar-nav ml-auto">
        <ul class="navbar-nav">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="login.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>



    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar code -->

            <!-- Main content -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-12 px-md-4 main-content">
                <div class="header">
                    <h2>Pending Posts</h2>
                </div>

                <section id="main-content">
                    <section class="wrapper">
                        <div class="row">
                            <div class="col-md-12">
                                 <table id="myTable" class="table table-bordered table-hover">
                                    
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>Location</th>
                                            <th style="width: 350px;">Image</th>
                                            <th style="width: 150px;">Current Status</th>
                                            <th style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($property = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($property['name']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($property['description']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($property['price']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($property['addr']); ?>
                                                </td>
                                                <td>

                                                    <?php if (!empty($property['image2']) || !empty($property['image3'])): ?>
                                                        <div id="carousel<?php echo $property['id']; ?>" class="carousel slide"
                                                            data-ride="carousel">
                                                            <!-- Indicators -->
                                                            <ol class="carousel-indicators">
                                                                <?php if (!empty($property['image1'])): ?>
                                                                    <li data-target="#carousel<?php echo $property['id']; ?>"
                                                                        data-slide-to="0" class="active"></li>
                                                                <?php endif; ?>
                                                                <?php if (!empty($property['image2'])): ?>
                                                                    <li data-target="#carousel<?php echo $property['id']; ?>"
                                                                        data-slide-to="1"></li>
                                                                <?php endif; ?>
                                                                <?php if (!empty($property['image3'])): ?>
                                                                    <li data-target="#carousel<?php echo $property['id']; ?>"
                                                                        data-slide-to="2"></li>
                                                                <?php endif; ?>
                                                                <?php if (!empty($property['image4'])): ?>
                                                                    <li data-target="#carousel<?php echo $property['id']; ?>"
                                                                        data-slide-to="3"></li>
                                                                <?php endif; ?>


                                                            </ol>
                                                            <!-- Wrapper for slides -->
                                                            <div class="carousel-inner">
                                                                <?php if (!empty($property['image2'])): ?>
                                                                    <div class="item active">
                                                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($property['image2']); ?>"
                                                                            alt="Property Image 2"
                                                                            style="width:100px; height: 100px;">
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (!empty($property['image3'])): ?>
                                                                    <div class="item">
                                                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($property['image3']); ?>"
                                                                            alt="Property Image 3"
                                                                            style="width:100px; height: 100px;">
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (!empty($property['image4'])): ?>
                                                                    <div class="item">
                                                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($property['image4']); ?>"
                                                                            alt="Property Image 4"
                                                                            style="width:100px; height: 100px;">
                                                                    </div>
                                                                <?php endif; ?>

                                                                <?php if (!empty($property['image5'])): ?>
                                                                    <div class="item">
                                                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($property['image5']); ?>"
                                                                            alt="Property Image 5"
                                                                            style="width:100px; height: 100px;">
                                                                    </div>
                                                                <?php endif; ?>

                                                            </div>
                                                            <!-- Controls -->
                                                            <a class="left carousel-control"
                                                                href="#carousel<?php echo $property['id']; ?>" role="button"
                                                                data-slide="prev">
                                                                <span class="glyphicon glyphicon-chevron-left"
                                                                    aria-hidden="true"></span>
                                                                <span class="sr-only">Previous</span>
                                                            </a>
                                                            <a class="right carousel-control"
                                                                href="#carousel<?php echo $property['id']; ?>" role="button"
                                                                data-slide="next">
                                                                <span class="glyphicon glyphicon-chevron-right"
                                                                    aria-hidden="true"></span>
                                                                <span class="sr-only">Next</span>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <?php echo getStatusText($property['stat_code']); ?>
                                                </td>
                                                <td>
                                                    <form method="POST">
                                                        <input type="hidden" name="property_id"
                                                            value="<?php echo $property['id']; ?>">
                                                        <button type="submit" name="change_status" value="approve"
                                                            class="btn btn-success btn-xs"
                                                            onclick="return confirm('Are you sure you want to approve this property?');">
                                                            Approve <i class="fas fa-check" aria-hidden="true"></i>
                                                        </button>
                                                        <br><br>
                                                        <button type="submit" name="change_status" value="reject"
                                                            class="btn btn-danger btn-xs"
                                                            onclick="return confirm('Are you sure you want to reject this property?');">
                                                            Reject <i class="fas fa-times" aria-hidden="true"></i>
                                                        </button>
                                                    </form>

                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </section>


                <script>
                    function confirmChangeStatus(status, propertyId) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: `Do you want to ${status} this property?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, do it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Find the specific form for this property
                                var form = document.getElementById('statusForm' + propertyId);

                                // Add a hidden field to the form for change_status
                                var hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'change_status';
                                hiddenInput.value = status === 'approve' ? 'approve' : 'reject';
                                form.appendChild(hiddenInput);

                                // Submit the form
                                form.submit();
                            }
                        });
                    }
                </script>

                <script src="assets/js/jquery.min.js"></script>
                <script src="assets/js/bootstrap.min.js"></script>
            </main>
        </div>
    </div>
    <!-- JS scripts remain the same -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

     <!-- Include jQuery and DataTables scripts -->
     <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

    <!-- Initialize DataTables -->
    <script>
    $(document).ready(function() {
        $('#myTable').DataTable({
           
        });
    });
    </script>
</body>

</html>