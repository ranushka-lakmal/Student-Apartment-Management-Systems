<?php
require_once 'dbConnection.php'; // Make sure this path is correct

if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Prepare and bind
    $stmt = $con->prepare("INSERT INTO locations (latitude, longitude) VALUES (?, ?)");
    $stmt->bind_param("dd", $latitude, $longitude);

    if ($stmt->execute()) {
        echo "Location saved successfully. <a href='index.html'>Go back</a>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
} else {
    echo "No data provided";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Geolocation Picker</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <style>
        #mapid { height: 180px; }
    </style>
</head>
<body>

<div id="mapid"></div>
<form action="test.php" method="post">
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
    <button type="submit">Save Location</button>
</form>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    var mymap = L.map('mapid').setView([7.8731, 80.7718], 8); // Set view to Sri Lanka
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(mymap);

    var marker;

    mymap.on('click', function(e) {
        if (marker) {
            mymap.removeLayer(marker);
        }
        marker = L.marker(e.latlng).addTo(mymap);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
    });
</script>

</body>
</html>

