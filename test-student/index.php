<?php session_start();
require_once ('../config/dbconnection.php');
?>

<?php include 'header.php'; ?>




<!DOCTYPE html>
<html>

<head>
  <title>Login System</title>
  <link href="css/style.css" rel='stylesheet' type='text/css' />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="keywords"
    content="Elegent Tab Forms,Login Forms,Sign up Forms,Registration Forms,News latter Forms,Elements" . />
  <script
    type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
  </script>
  <script src="js/jquery.min.js"></script>
  <script src="js/easyResponsiveTabs.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function () {
      $('#horizontalTab').easyResponsiveTabs({
        type: 'default',
        width: 'auto',
        fit: true
      });
    });
  </script>
  <link
    href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,400,600,700,200italic,300italic,400italic,600italic|Lora:400,700,400italic,700italic|Raleway:400,500,300,600,700,200,100'
    rel='stylesheet' type='text/css'>


  <style>
    .property-card {
      margin: 0 10px;
      /* Add horizontal margin for spacing */
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
      border-radius: 10px;
      overflow: hidden;
      background: #fff;
    }

    .property-image-holder {
      height: 200px;
      background-color: #eee;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .property-image {
      max-width: 100%;
      max-height: 100%;
    }

    .property-info {
      padding: 5px;
    }

    .property-description {
      height: 60px;
      overflow: hidden;
    }

    .view-details-btn {
      background-color: #4CAF50;
      /* Example button color, change as needed */
      color: white;
      text-align: center;
      padding: 10px 20px;
      text-decoration: none;
      display: block;
      border-radius: 0 0 8px 8px;
    }

  </style>



</head>

<body>


  <div class="">


    <div id="slider" class="sl-slider-wrapper">

      <div class="sl-slider">

        <div class="sl-slide" data-orientation="horizontal" data-slice1-rotation="-25" data-slice2-rotation="-25"
          data-slice1-scale="2" data-slice2-scale="2">
          <div class="sl-slide-inner">
            <div class="bg-img bg-img-1"></div>


          </div>
        </div>

        <div class="sl-slide" data-orientation="vertical" data-slice1-rotation="10" data-slice2-rotation="-15"
          data-slice1-scale="1.5" data-slice2-scale="1.5">
          <div class="sl-slide-inner">
            <div class="bg-img bg-img-2"></div>

          </div>
        </div>

        <div class="sl-slide" data-orientation="horizontal" data-slice1-rotation="3" data-slice2-rotation="3"
          data-slice1-scale="2" data-slice2-scale="1">
          <div class="sl-slide-inner">
            <div class="bg-img bg-img-3"></div>
            < </div>
          </div>

          <div class="sl-slide" data-orientation="vertical" data-slice1-rotation="-5" data-slice2-rotation="25"
            data-slice1-scale="2" data-slice2-scale="1">
            <div class="sl-slide-inner">
              <div class="bg-img bg-img-4"></div>

            </div>
          </div>

          <div class="sl-slide" data-orientation="horizontal" data-slice1-rotation="-5" data-slice2-rotation="10"
            data-slice1-scale="2" data-slice2-scale="1">
            <div class="sl-slide-inner">
              <div class="bg-img bg-img-5"></div>

            </div>
          </div>
        </div><!-- /sl-slider -->



        <nav id="nav-dots" class="nav-dots">
          <span class="nav-dot-current"></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
        </nav>

      </div><!-- /slider-wrapper -->
    </div>



    <div class="banner-search">
      <div class="container">
        <!-- banner -->
        <h3>Find your accomerdation</h3>
        <div class="searchbar">
          <div class="row">
            <div class="col-lg-6 col-sm-6">
              <input type="text" class="form-control" placeholder="Search of Properties">
              <div class="row">

                <div class="col-lg-3 col-sm-4">
                  <select class="form-control">
                    <option>Price</option>
                    <option>$150,000 - $200,000</option>
                    <option>$200,000 - $250,000</option>
                    <option>$250,000 - $300,000</option>
                    <option>$300,000 - above</option>
                  </select>
                </div>
                <div class="col-lg-3 col-sm-4">
                  <select class="form-control">
                    <option>Colombo</option>
                    <option>Homagama</option>
                    <option>Athurugiriya</option>
                    <option>Pitipana</option>
                  </select>
                </div>
                <div class="col-lg-3 col-sm-4">
                  <button class="btn btn-success" onclick="window.location.href='buysalerent.php'">Find Now</button>
                </div>
              </div>


            </div>
            <div class="col-lg-5 col-lg-offset-1 col-sm-6 ">
              <p>Join now and get updated.</p>
              <button class="btn btn-info" data-toggle="modal" data-target="#loginpop">Login</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- banner -->
    <div class="container">

      <div class="properties-listing spacer">
        
        <h2>Featured Properties</h2>
        <div id="owl-example" class="owl-carousel">
          <?php
          $query = "SELECT id, landlord_email, name, description, stat_code, image1 FROM properties WHERE stat_code='0'";
          $result = $con->query($query);

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              if (!empty($row['image1'])) {
                $imageData = base64_encode($row['image1']);
                $src = 'data:image/jpeg;base64,' . $imageData;
              } else {
                $src = 'path/to/default/image.jpg'; // Path to a default image
              }

              echo '<div class="property-card">';
              echo '<div class="property-image-holder">';
              echo '<img src="' . htmlspecialchars($src) . '" class="property-image" alt="Property Image"/>';
              echo '</div>';
              echo '<div class="property-info">';
              echo '<h4><a href="property-detail.php?id=' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</a></h4>';
              echo '<div class="property-description">' . htmlspecialchars($row['description']) . '</div>';
              echo '</div>';
              echo '<a href="property-detail.php?id=' . $row['id'] . '" class="view-details-btn">View Details</a>';
              echo '</div>';
            }
          } else {
            echo "<p>No featured properties found.</p>";
          }
          ?>
        </div>
      </div>




    </div>



  </div>
  <?php include 'footer.php'; ?>

  <?php $con->close(); ?>


  <script>
    $(document).ready(function () {
      $("#owl-example").owlCarousel({
        margin: 20,
        loop: true,
        responsive: {
          0: { items: 1 },
          600: { items: 2 },
          1000: { items: 3 }
        }
      });
    });
  </script>


</body>

</html>