<?php
session_start();
require_once 'dbconnection.php'; // Update this path as needed

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

// Initialize variables to store data for the dashboard
$newPost = 0; // Assume this is what 'stat_code' 1 represents
// ... initialize other variables as needed


$query = "SELECT COUNT(*) as newPost FROM properties WHERE stat_code=1";
$result = mysqli_query($con, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $newPost = $row['newPost'];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>Admin | Update Profile</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">

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

        /* Add more color styles for other info-boxes */

        /* Responsive tweaks */
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




</body>

</html>