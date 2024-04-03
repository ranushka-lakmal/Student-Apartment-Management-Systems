<?php
session_start();
require_once ('../config/dbconnection.php');


$message = '';
$updateFields = [];
$propertyId = $_GET['id'] ?? null;
$landlordEmail = $_SESSION['login'];
$property = [];


if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}


$query = "SELECT * FROM properties WHERE id = ? AND landlord_email = ?";
if ($stmt = mysqli_prepare($con, $query)) {
    mysqli_stmt_bind_param($stmt, 'is', $propertyId, $landlordEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $property = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $addr = $_POST['addr'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];


    $updateQuery = "UPDATE properties SET name=?, description=?, price=?, addr=?, latitude=?, longitude=? WHERE id=? AND landlord_email=?";
    if ($stmt = mysqli_prepare($con, $updateQuery)) {
        mysqli_stmt_bind_param($stmt, 'ssdsddis', $name, $description, $price, $addr, $latitude, $longitude, $propertyId, $landlordEmail);
        mysqli_stmt_execute($stmt);
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        if ($affected_rows > 0) {
            $message = "updated successfully.";
        } else {
            $message = "No changes were made.";
        }
    } else {
        $message = "Failed to update property.";
    }


    for ($i = 1; $i <= 5; $i++) {
        if (isset($_FILES["image$i"]) && $_FILES["image$i"]['error'] === UPLOAD_ERR_OK) {
            $imageContent = file_get_contents($_FILES["image$i"]['tmp_name']);
            $imageQuery = "UPDATE properties SET image$i = ? WHERE id = ? AND landlord_email = ?";
            if ($imageStmt = mysqli_prepare($con, $imageQuery)) {
                mysqli_stmt_bind_param($imageStmt, 'bis', $null, $propertyId, $landlordEmail);
                mysqli_stmt_send_long_data($imageStmt, 0, $imageContent);
                mysqli_stmt_execute($imageStmt);
                mysqli_stmt_close($imageStmt);
            }
        }
    }

    $query = "SELECT * FROM properties WHERE id = ? AND landlord_email = ?";
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, 'is', $propertyId, $landlordEmail);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $property = mysqli_fetch_assoc($result);
            $message = "updated successfully.";
        } else {
            $message = "Failed to refresh the property information.";
        }
        mysqli_stmt_close($stmt);
    }


}


mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Include Bootstrap JS and its dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
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

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 0 8px rgba(0, 123, 255, 0.6);
        }

        .btn-submit {
            padding: 10px 25px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #333;
        }

        .image-upload-wrapper {
            border: 2px dashed #007bff;
            border-radius: 8px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease-in-out;
        }

        .image-upload-wrapper:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 15px rgba(0, 123, 255, 0.2);
        }

        .image-upload-wrapper img {
            width: 100%;
            border-radius: 8px;
        }

        .image-upload-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }
    </style>

</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container mt-5">

        <h2>Edit Property</h2>
        <?php if ($message): ?>
            <div class="alert <?php echo $error ? 'alert-danger' : 'alert-success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>


        <form method="post" enctype="multipart/form-data">
            <div class="image-upload-container">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="image-upload-wrapper"
                        onclick="document.getElementById('imageInput<?php echo $i; ?>').click()">
                        <img id="previewImage<?php echo $i; ?>"
                            src="data:image/jpeg;base64,<?php echo base64_encode($property["image$i"]); ?>"
                            alt="Image <?php echo $i; ?>" style="height: 150px; cursor:pointer;" />
                        <input type="file" id="imageInput<?php echo $i; ?>" name="image<?php echo $i; ?>" accept="image/*"
                            onchange="previewImage(event, <?php echo $i; ?>)" style="display: none;">
                    </div>
                <?php endfor; ?>
            </div>

            <div>
                <label for="name">Name</label>
                <input type="text" id="name" name="name"
                    value="<?php echo htmlspecialchars($property['name'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" name="description"
                    required><?php echo htmlspecialchars($property['description'] ?? '', ENT_QUOTES); ?></textarea>
            </div>
            <div>
                <label for="price">Price</label>
                <input type="number" id="price" name="price"
                    value="<?php echo htmlspecialchars($property['price'] ?? '', ENT_QUOTES); ?>" step="0.01" required>
            </div>
 

            <div class="form-group">
                <label for="addr">Location</label>
                <input type="text" class="form-control" id="addr" name="addr" value="<?php echo htmlspecialchars($property['addr'] ?? '', ENT_QUOTES); ?>" required readonly>
            </div>

            <div id="mapid" style="height: 400px;"></div>
            <input type="hidden" id="latitude" name="latitude"
                value="<?php echo htmlspecialchars($property['latitude'] ?? 0); ?>">
            <input type="hidden" id="longitude" name="longitude"
                value="<?php echo htmlspecialchars($property['longitude'] ?? 0); ?>">

                    <hr>    


            <button type="submit" name="submit" class="btn-submit">Update Property</button>
        </form>
    </div>

    <!-- Flash Message Modal -->
    <div class="modal fade" id="flashMessageModal" tabindex="-1" role="dialog" aria-labelledby="flashMessageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="flashMessageModalLabel">Update Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php echo $message; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        function previewImage(event, index) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById('previewImage' + index);
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

    <script>
        <?php if ($message): ?>
            $(document).ready(function () {
                $('#flashMessageModal').modal('show');

                setTimeout(function () {
                    $('#flashMessageModal').modal('hide');
                }, 3000);
            });
        <?php endif; ?>
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var map = L.map('mapid').setView([<?php echo $property['latitude'] ?: '7.8731'; ?>, <?php echo $property['longitude'] ?: '80.7718'; ?>], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        var marker = L.marker([<?php echo $property['latitude'] ?: '7.8731'; ?>, <?php echo $property['longitude'] ?: '80.7718'; ?>], {
            draggable: true
        }).addTo(map).on('dragend', function (e) {
            var position = marker.getLatLng();
            $("#latitude").val(position.lat);
            $("#longitude").val(position.lng);
            reverseGeocode(position.lat, position.lng);
        });

        function reverseGeocode(lat, lng) {
            var url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    $("#addr").val(data.address.town || data.address.city || data.address.village || 'Not found');
                })
                .catch(err => console.error(err));
        }

      
        reverseGeocode(<?php echo $property['latitude'] ?: '7.8731'; ?>, <?php echo $property['longitude'] ?: '80.7718'; ?>);
    </script>

</body>

</html>