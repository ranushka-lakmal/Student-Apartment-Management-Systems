<?php
include 'dbconnection.php'; // Make sure this path is correct

$propertyId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM properties WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $property = $result->fetch_assoc();
  // Assuming images are stored in blob format
  $images = [];
  for ($i = 1; $i <= 5; $i++) {
    if (!empty($property["image$i"])) {
      $images[] = 'data:image/jpeg;base64,' . base64_encode($property["image$i"]);
    }
  }

  // Fetch the landlord's contact number in a separate query
  $contactQuery = "SELECT contactno FROM users WHERE email = ?";
  $contactStmt = $con->prepare($contactQuery);
  $contactStmt->bind_param("s", $property['landlord_email']);
  $contactStmt->execute();
  $contactResult = $contactStmt->get_result();
  if ($contactResult->num_rows > 0) {
    $contactInfo = $contactResult->fetch_assoc();
    $landlordContact = $contactInfo['contactno'];
  } else {
    $landlordContact = "Contact number not available";
  }
  $contactStmt->close();

} else {
  echo "<p>Property not found.</p>";
}

// Handle reservation action
if(isset($_POST['reserve']) && $propertyId > 0) {
  $updateQuery = "UPDATE properties SET is_reserve = 1 WHERE id = ? AND landlord_email = ?";
  $stmt = mysqli_prepare($con, $updateQuery);
  mysqli_stmt_bind_param($stmt, 'is', $propertyId, $landlordEmail);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
  
  // Redirect to prevent form resubmission
  header("Location: property-detail_test.php?id={$propertyId}");
  exit;
}


$stmt->close();
?>


<?php include 'header.php'; ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- banner -->
<div class="inside-banner">
  <div class="container">
    <span class="pull-right"><a href="#">Home</a> / Reserve Apartment</span>
    <h2>Reservation</h2>
  </div>
</div>
<!-- banner -->


<div class="container">
  <div class="properties-listing spacer">

    <div class="row">
      <div class="col-lg-3 col-sm-4 hidden-xs">

        <div class="hot-properties hidden-xs">
          <h4>Latest Properties</h4>

          <div class="col-lg-9 col-sm-8 ">
            <?php
            require_once ('dbconnection.php'); // Adjust the path as needed
            
            $propertyId = isset($_GET['id']) ? (int) $_GET['id'] : 0; 
            
        
            $stmt = $con->prepare("SELECT * FROM properties WHERE id = ?");
            $stmt->bind_param("i", $propertyId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
              $property = $result->fetch_assoc();
       
              $image1 = $property['image1'] ? 'data:image/jpeg;base64,' . base64_encode($property['image1']) : 'path_to_default_image.jpg';

              echo "<h2>" . htmlspecialchars($property['name']) . "</h2>";
              echo '<div class="property-images">';
              echo '<img src="' . $image1 . '" alt="Property Image" class="properties" />';
              echo '</div>';
              echo '<div class="spacer"><h4><span class="glyphicon glyphicon-th-list"></span> Property Details</h4>';
              echo '<p>' . htmlspecialchars($property['description']) . '</p></div>';

              
            } else {
              echo "<p>Property not found.</p>";
            }

            $stmt->close();
            ?>
          </div>

        </div>

        <div class="spacer">

          <?php if (!empty($property['landlord_contact'])): ?>
            <p><strong>Contact Number:</strong>
              <?php echo htmlspecialchars($property['landlord_contact']); ?>
            </p>
          <?php endif; ?>
        </div>


        <div class="advertisement">
          <h4>Advertisements</h4>
          <img src="images/advertisements.jpg" class="img-responsive" alt="advertisement">

        </div>

      </div>

      <div class="col-lg-9 col-sm-8 ">

        <h2>
          <?php echo htmlspecialchars($property['name']); ?>
        </h2>
        <div class="row">
          <div class="col-lg-8">
            <div class="property-images">
            
              <div class="row">
                <div class="col-lg-12 col-sm-12">
                
                  <?php if (!empty($images)): ?>
                    <div id="propertyImagesCarousel" class="carousel slide" data-ride="carousel">
                     
                      <ol class="carousel-indicators">
                        <?php foreach ($images as $index => $src): ?>
                          <li data-target="#propertyImagesCarousel" data-slide-to="<?php echo $index; ?>" <?php if ($index == 0)
                               echo 'class="active"'; ?>></li>
                        <?php endforeach; ?>
                      </ol>
                    
                      <div class="carousel-inner">
                        <?php foreach ($images as $index => $src): ?>
                          <div class="item <?php if ($index == 0)
                            echo 'active'; ?>">
                            <img src="<?php echo $src; ?>" alt="Property image <?php echo $index + 1; ?>">
                          </div>
                        <?php endforeach; ?>
                      </div>
                     
                      <a class="left carousel-control" href="#propertyImagesCarousel" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                        <span class="sr-only">Previous</span>
                      </a>
                      <a class="right carousel-control" href="#propertyImagesCarousel" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                        <span class="sr-only">Next</span>
                      </a>
                    </div>
                  <?php else: ?>
                    <p>No images available for this property.</p>
                  <?php endif; ?>
                
                  <div class="spacer">
                    <h4><span class="glyphicon glyphicon-th-list"></span> Property Details</h4>
                    <p>
                      <?php echo htmlspecialchars($property['description']); ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>


            <div>
              <h4><span class="glyphicon glyphicon-map-marker"></span> Location</h4>
          
              <div class="well">
                <!-- Map Container -->
                <div id="mapid" style="height: 350px;"></div>
              </div>

              <script>
                // Leaflet.js Map Initialization
                var latitude = <?php echo json_encode(floatval($property['latitude'])); ?>;
                var longitude = <?php echo json_encode(floatval($property['longitude'])); ?>;

                // Only attempt to render the map if latitude and longitude are available
                if (latitude && longitude) {
                  var map = L.map('mapid').setView([latitude, longitude], 13);
                  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                  }).addTo(map);

                  var marker = L.marker([latitude, longitude]).addTo(map)
                    .bindPopup('<?php echo htmlspecialchars($property['name'], ENT_QUOTES); ?>').openPopup();
                } else {
                  document.getElementById('mapid').innerHTML = "No location data available for this property.";
                }
              </script>
            </div>


          </div>
          <div class="col-lg-4">
            <div class="col-lg-12  col-sm-6">
              <div class="property-info">
                <p class="price"> RS.
                  <?php echo htmlspecialchars($property['price']); ?>
                </p>
                <p class="area"><span class="glyphicon glyphicon-map-marker"></span>
                  <?php echo htmlspecialchars($property['addr']); ?>
                </p>

                <div class="profile">


                  <div class="spacer">
                    <h4><span class="glyphicon glyphicon-th-list"></span> Contact Details</h4>

                    <p>
                      <strong>Email:</strong>
                      <?php echo htmlspecialchars($property['landlord_email']); ?>
                    </p>

                    <?php if (isset($landlordContact)): ?>
                      <p><strong>Contact Number:</strong>
                        <strong>
                          <?php echo htmlspecialchars($landlordContact); ?>
                        </strong>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <h6><span class="glyphicon glyphicon-home"></span> Availabilty</h6>
              <div class="listing-detail">
               
<!-- Reserve Button -->
<?php if ($property['is_reserve'] == 0): ?>
    <button id="reserveButton" class="btn btn-primary">Reserve</button>
<?php else: ?>
    <button class="btn btn-secondary" disabled>Reserved</button>
<?php endif; ?>


              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>



<script>
document.getElementById('reserveButton').addEventListener('click', function() {
    var propertyId = <?php echo $propertyId; ?>;
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'reserve_property.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            // Success: Reload the page or update the UI accordingly
            location.reload();
        }
    };
    xhr.send('property_id=' + propertyId);
});
</script>
<?php if (!empty($property['landlord_contact'])): ?>
  <p><strong>Contact Number:</strong>
    <?php echo htmlspecialchars($property['landlord_contact']); ?>
  </p>
<?php endif; ?>
<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>