<?php
session_start();
require_once 'dbconnection.php';

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
    $addr1 = trim($_POST['addr1']);
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

        $stmt = $con->prepare("INSERT INTO properties (landlord_email, name, description, price, addr, addr1, stat_code, image1, image2, image3, image4, image5, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param('sssdsssssssdd', $landlordEmail, $propertyName, $propertyDescription, $propertyPrice, $propertyLocation, $addr1, $imagesData[0], $imagesData[1], $imagesData[2], $imagesData[3], $imagesData[4], $latitude, $longitude);

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        .image-upload-container {
            display: flex;
            justify-content: start;
            gap: 50px;
            margin-bottom: 20px;
        }

        .image-upload-wrapper {
            border: 1px dashed #ccc;
            height: 200px;
            width: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
        }

        .image-upload-wrapper img {
            max-width: 100%;
            max-height: 100%;
            display: none;
        }

        .image-upload-input {
            position: absolute;
            opacity: 0;
            height: 100%;
            width: 100%;
            cursor: pointer;
        }

        .add-photo-text {
            pointer-events: none;
        }

        #mapid {
            height: 300px;
        }

        .container {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            margin: 40px auto;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
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

            <div class="image-upload-container">
                <!-- Image Upload Inputs -->
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <div class="image-upload-wrapper">
                        <input type="file" name="images[]" class="image-upload-input"
                            onchange="previewImage(event, <?php echo $i; ?>)">
                        <img id="preview<?php echo $i; ?>" style="display: none;">
                        <div class="add-photo-text">Add Photo</div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="form-group">
                <label for="propertyName">Property Name</label>
                <input type="text" class="form-control" id="propertyName" name="propertyName" required>
            </div>
            <div class="form-group">
                <label for="propertyDescription">Description</label>
                <textarea class="form-control" id="propertyDescription" name="propertyDescription" rows="3"
                    required></textarea>
            </div>
            <div class="form-group">
                <label for="propertyPrice">Price</label>
                <input type="text" class="form-control" id="propertyPrice" name="propertyPrice" required>
            </div>

            <div class="form-group">
                <label for="propertyLocation">Home Town</label>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="propertyLocation" name="propertyLocation" required>
                    <div class="input-group-append">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="addr1">Address</label>
                <input type="text" class="form-control" id="addr1" name="addr1" required>
            </div>


            <div id="mapid"></div>
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <hr>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        function previewImage(event, index) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('preview' + index);
                output.src = reader.result;
                output.style.display = 'block';
                output.nextElementSibling.style.display = 'none'; // Hide the 'Add a photo' text
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // Initialize the Leaflet map
        var mymap = L.map('mapid').setView([7.8731, 80.7718], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(mymap);

        // Add a marker and update its position on map click
        var marker = L.marker([7.8731, 80.7718], { draggable: true }).addTo(mymap).on('dragend', onMarkerDrag);
        mymap.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateLocationDetails(e.latlng.lat, e.latlng.lng);
        });

        // Update location details and perform reverse geocoding to fetch the hometown name
        function updateLocationDetails(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            reverseGeocode(lat, lng);
        }

        function onMarkerDrag(e) {
            var latlng = e.target.getLatLng();
            updateLocationDetails(latlng.lat, latlng.lng);
        }

        // Reverse geocoding to get the hometown name
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
        document.getElementById('propertyLocation').addEventListener('input', debounce(function (event) {
            geocodeAddress(event.target.value);
        }, 500)); // Debounce with a 500ms delay

        function geocodeAddress(address) {
            if (address.length < 5) return; // Optionally, wait for a minimum length of address to reduce requests
            var url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        var lat = data[0].lat;
                        var lon = data[0].lon;
                        mymap.setView([lat, lon], 13);
                        if (!marker) {
                            marker = L.marker([lat, lon], { draggable: true }).addTo(mymap).on('dragend', onMarkerDrag);
                        } else {
                            marker.setLatLng([lat, lon]);
                        }
                        updateLocationDetails(lat, lon);
                    }
                })
                .catch(err => console.error(err));
        }

        function debounce(func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }

        function onMarkerDrag(e) {
            var latlng = e.target.getLatLng();
            updateLocationDetails(latlng.lat, latlng.lng);
        }

        function updateLocationDetails(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }
    </script>


</body>

</html>