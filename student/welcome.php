<?php
session_start();
if (strlen($_SESSION['id']==0)) {
  header('location:logout.php');
  } else {
    require_once ('../config/dbconnection.php');
    $query = "SELECT name, price, addr, image1 FROM properties WHERE stat_code='0'";
    $result = $con->query($query);

?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Welcome </title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/heroic-features.css" rel="stylesheet">


    <style>
        .property-listing {
            margin-bottom: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .property-listing h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
       
        .property-price {
            color: #28a745;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .property-info {
            margin-bottom: 5px;
            font-size: 16px;
        }

        .property-listing img {
            width: 40%; 
            border-radius: 4px;
            margin-bottom: 15px; 

        .property-listing .property-details {
            padding-left: 15px;
        }
        .property-listing h2,
        .property-listing .property-price,
        .property-listing .property-info {
            margin: 0;
            padding: 2px 0; /* Spacing for text */
             font-size: 22px;
            margin-bottom: 10px;
        }


        .property-listing:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .property-listing .property-info {
            font-size: 16px;
            color: #555555;
            margin-bottom: 5px;
        }

        @media (min-width: 768px) {
            .centered {
                float: none;
                margin: 0 auto;
            }
        }


    </style>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Welcome !</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="#"><?php echo $_SESSION['name'];?></a>
                    </li>
                    <li>
                        <a href="logout.php">Logout</a>
                    </li>
                  
                </ul>
            </div>
        </div>
    </nav>


    <div class="container">
        <!-- Property Listings -->
        <div class="row">
            <?php while ($property = $result->fetch_assoc()): ?>
                <div class="col-xs-8 property-listing">
                    <div class="row">
                        <div class="col-md-4">
                            <?php
                            if (!empty($property['image1'])) {
                                $imageData = base64_encode($property['image1']);
                                $src = 'data:image/jpeg;base64,' . $imageData;
                                echo '<img src="' . $src . '" alt="Property Image">';
                            } else {
                                echo 'No image available';
                            }
                            ?>
                        </div>
                        <div class="col-md-8 property-details">
                            <h2><?php echo htmlspecialchars($property['name']); ?></h2>
                            <div class="property-price">Rs <?php echo htmlspecialchars($property['price']); ?></div>
                            <div class="property-info">Location: <?php echo htmlspecialchars($property['addr']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>


    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>


<?php 
$con->close();
} 
?>