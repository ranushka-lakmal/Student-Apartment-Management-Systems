<!DOCTYPE html>
<html>
<head>
    <title>Geolocation Picker</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <style>
        #mapid { height: 400px; }
    </style>
</head>
<body>

<div id="mapid"></div>
<form action="test2.php" method="post">
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
    <button type="submit">Save Location</button>
</form>

<?php
require_once 'dbConnection.php';  

// Fetch locations from the database
$query = "SELECT * FROM locations where id = 2";
$result = mysqli_query($con, $query);
$locations = [];
while($row = mysqli_fetch_assoc($result)) {
    $locations[] = $row;
}
?>

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

    // Add saved locations to the map
    var locations = <?php echo json_encode($locations); ?>;
    locations.forEach(function(location) {
        var marker = L.marker([location.latitude, location.longitude]).addTo(mymap);
        marker.bindPopup(location.created_at); // Assuming you want to show the creation date as a label
    });
</script>

</body>
</html>
