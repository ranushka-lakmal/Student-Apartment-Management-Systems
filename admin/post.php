<?php
session_start();
require_once 'dbconnection.php'; // Update this path as needed

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Property Listings</title>
    <!-- Bootstrap CSS from CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- jQuery from CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Bootstrap JavaScript from CDN -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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

        /* Adding some shadow to the card footer */
    </style>
</head>

<body>

    <section id="container">
        <header class="header header-bg">
            <div class="sidebar-toggle-box">
                <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
            </div>
            <a href="#" class="logo"><b>Admin Dashboard</b></a>
            <div class="nav notify-row" id="top_menu">


                </ul>
            </div>
            <div class="top-menu">
                <ul class="nav pull-right top-menu">
                    <li><a class="logout" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </header>

        <?php include ('sidebar.php'); ?>

        <br>



        <section id="main-content">
            <section class="wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover">
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
                                                                    alt="Property Image 2" style="width:100px; height: 100px;">
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($property['image3'])): ?>
                                                            <div class="item">
                                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($property['image3']); ?>"
                                                                    alt="Property Image 3" style="width:100px; height: 100px;">
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($property['image4'])): ?>
                                                            <div class="item">
                                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($property['image4']); ?>"
                                                                    alt="Property Image 4" style="width:100px; height: 100px;">
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if (!empty($property['image5'])): ?>
                                                            <div class="item">
                                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($property['image5']); ?>"
                                                                    alt="Property Image 5" style="width:100px; height: 100px;">
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
        <form method="POST" id="statusForm<?php echo $property['id']; ?>">
            <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
            <button type="button" class="btn btn-success btn-xs"
                onclick="confirmChangeStatus('approve', <?php echo $property['id']; ?>);">
                Approve <i class="fas fa-check" aria-hidden="true"></i>
            </button>
            <button type="button" class="btn btn-danger btn-xs"
                onclick="confirmChangeStatus('reject', <?php echo $property['id']; ?>);">
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
</body>

</html>