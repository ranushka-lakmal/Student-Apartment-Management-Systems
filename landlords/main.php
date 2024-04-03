<?php
session_start();
require_once 'dbconnection.php'; // Make sure this is the correct path to your database connection script

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $landlordEmail = $_SESSION['login'];
    $propertyName = trim($_POST['propertyName']);
    $propertyDescription = trim($_POST['propertyDescription']);
    $propertyPrice = trim($_POST['propertyPrice']);
    $propertyLocation = trim($_POST['propertyLocation']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    

    // Validate required fields
    if (empty($propertyName) || empty($propertyDescription) || empty($propertyPrice) || empty($propertyLocation) || empty($latitude) || empty($longitude)) {
        $message = 'All fields including the property location on the map are required.';
        $error = true;
    } elseif (!is_numeric($propertyPrice) || $propertyPrice <= 0) {
        $message = 'Please enter a valid price.';
        $error = true;
    } else {
        $imagesData = [];
        for ($i = 0; $i < 5; $i++) {
            $imagesData[] = isset($_FILES['images']['tmp_name'][$i]) && is_uploaded_file($_FILES['images']['tmp_name'][$i]) ? file_get_contents($_FILES['images']['tmp_name'][$i]) : null;
        }

        $stmt = $con->prepare("INSERT INTO properties (landlord_email, name, description, price, addr, stat_code, image1, image2, image3, image4, image5, latitude, longitude) VALUES (?, ?, ?, ?, ?, 'active', ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param('sssdssssssdds', $landlordEmail, $propertyName, $propertyDescription, $propertyPrice, $propertyLocation, $imagesData[0], $imagesData[1], $imagesData[2], $imagesData[3], $imagesData[4], $latitude, $longitude);

        if (!$stmt->execute()) {
            $error = true;
            $message = 'Error: ' . $stmt->error;
        } else {
            $message = 'Property added successfully!';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Property</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <style>
        .image-upload-container { display: flex; justify-content: start; gap: 10px; margin-bottom: 20px; }
        .image-upload-wrapper { border: 1px dashed #ccc; height: 200px; width: 200px; display: flex; align-items: center; justify-content: center; position: relative; cursor: pointer; }
        .image-upload-wrapper img { max-width: 100%; max-height: 100%; display: none; }
        .image-upload-input { position: absolute; opacity: 0; height: 100%; width: 100%; cursor: pointer; }
        .add-photo-text { pointer-events: none; }
        #mapid { height: 300px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <?php if ($message): ?>
    <div class="alert <?php echo $error ? 'alert-danger' : 'alert-success'; ?>" role="alert">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    <h2>Add Property Details</h2>
    <form action="main.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="propertyName">Property Name</label>
            <input type="text" class="form-control" id="propertyName" name="propertyName" required>
        </div>
        <div class="form-group">
            <label for="propertyDescription">Description</label>
            <textarea class="form-control" id="propertyDescription" name="propertyDescription" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="propertyPrice">Price</label>
            <input type="text" class="form-control" id="propertyPrice" name="propertyPrice" required>
        </div>
        <div class="form-group">
            <label for="propertyLocation">Location / Address</label>
            <input type="text" class="form-control" id="propertyLocation" name="propertyLocation" required>
        </div>

        <div id="mapid"></div>
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
// Function to preview images before uploading
function previewImage(event, index) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('preview' + index);
        output.src = reader.result;
        output.style.display = 'block';
        output.nextElementSibling.style.display = 'none'; // Hide the 'Add a photo' text
    };
    reader.readAsDataURL(event.target.files[index]);
}

// Initialize the Leaflet map
var mymap = L.map('mapid').setView([7.8731, 80.7718], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
}).addTo(mymap);

// Add a marker and update its position on map click
var marker = L.marker([7.8731, 80.7718], {draggable: true}).addTo(mymap).on('dragend', onMarkerDrag);
mymap.on('click', function(e) {
    marker.setLatLng(e.latlng);
    updateLocationDetails(e.latlng.lat, e.latlng.lng);
});


function updateLocationDetails(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    reverseGeocode(lat, lng);
}

function onMarkerDrag(e) {
    var latlng = e.target.getLatLng();
    updateLocationDetails(latlng.lat, latlng.lng);
}


function reverseGeocode(lat, lng) {
    var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            var propertyLocation = data.address.city || data.address.town || data.address.village || 'Not found';
            document.getElementById('propertyLocation').value = propertyLocation;
        })
        .catch(err => console.error(err));
}
</script>

</body>
</html>
