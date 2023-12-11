<?php
session_start();
include 'includes/db_connection.php';
$conn = connectToDatabase();



if (empty($_SESSION['username'])) {
    $_SESSION['callback_url'] = 'create_itinerary.php';
    // Redirect to login page
    header("Location: login.php");
}

//Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $tripName = $_POST["trip_name"];
    $location = $_POST["selected_location"];
    $tripDescription = $_POST["trip_description"];
    $status = $_POST["status"];
    $startDate = !empty($_POST["start_date"]) ? $_POST["start_date"] : null;
    $endDate = !empty($_POST["end_date"]) ? $_POST["end_date"] : null;
    $groupSize = $_POST["group_size"];

    // Set a temporary member_id value (replace it with the actual member_id logic)
    $memberId = 1;

    // Calculate duration if both start and end dates are filled
    $duration = null;
    if (!empty($startDate) && !empty($endDate)) {
        $startDateTime = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);
        $interval = $startDateTime->diff($endDateTime);
        $duration = $interval->days;
    }

    // Handle file upload
    $mainImg = null; // Initialize to null
    if (isset($_FILES['main_img']) && $_FILES['main_img']['error'] === UPLOAD_ERR_OK) {
        $tempName = $_FILES['main_img']['tmp_name'];
        $mainImg = file_get_contents($tempName);
    }

    // Perform database insertion
    $stmt = $conn->prepare("INSERT INTO itinerary (trip_name, trip_location, trip_description, status, duration, group_size, main_img, last_updated_date, creation_date, member_id, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?)");

    $stmt->bind_param("ssssiisiss", $tripName, $location, $tripDescription, $status, $duration, $groupSize, $mainImg, $memberId, $startDate, $endDate);

    // Execute the statement
    if ($stmt->execute()) {
        // Query the database to get the inserted row
        $result = $conn->query("SELECT * FROM itinerary ORDER BY itinerary_id DESC LIMIT 1");

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            echo "<h3>Submitted Data from Database:</h3>";
            echo "<p><strong>Trip Name:</strong> " . $row["trip_name"] . "</p>";
            echo "<p><strong>Location:</strong> " . $row["trip_location"] . "</p>";
            echo "<p><strong>Trip Description:</strong> " . $row["trip_description"] . "</p>";
            echo "<p><strong>Status:</strong> " . $row["status"] . "</p>";
            echo "<p><strong>Start Date:</strong> " . $row["start_date"] . "</p>";
            echo "<p><strong>End Date:</strong> " . $row["end_date"] . "</p>";
            echo "<p><strong>Duration:</strong> " . $row["duration"] . " days</p>";
            echo "<p><strong>Group Size:</strong> " . $row["group_size"] . "</p>";
            if (!empty($row["main_img"])) {
                echo "<p><strong>Main Image:</strong> <img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["main_img"]) . "' alt='Main Image'></p>";
            } else {
                echo "<p><strong>Main Image:</strong> No image available</p>";
            }

            // Redirect to edit_itinerary.php with the new itinerary's ID
            $itineraryId = $row["itinerary_id"];
            header("Location: edit_itinerary.php?id=$itineraryId");
            exit();
        } else {
            echo "Error fetching data from the database.";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Itinerary</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>
    <!-- css naming convention to be changed after the project -->
    <div class="auth-content">
        <h3>Create an Itinerary</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="itinerary-creation-form">

            <label for="trip_name">Trip Name:</label>
            <input type="text" id="trip_name" name="trip_name" placeholder="Enter trip name" required />

            <label for="location">Location:</label>
            <?php include 'includes/location_autocomplete.php'; ?>
            <input type="hidden" id="selected_location" name="selected_location" />


            <label for="trip_description">Trip Description:</label>
            <textarea id="trip_description" name="trip_description" placeholder="Enter trip description" rows="5" accesskey="" required></textarea>

            <div class="form-group">
                <div class="col">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="planning">Planning</option>
                        <option value="in_progress">In Progress</option>
                        <option value="complete">Complete</option>
                    </select>
                </div>

                <div class="col">
                    <label for="group_size">Group Size (optional):</label>
                    <input type="number" id="group_size" name="group_size" placeholder="Enter group size" />
                </div>
            </div>
            <div class="form-group">
                <div class="col">
                    <label for="start_date">Start Date (optional):</label>
                    <input type="date" id="start_date" name="start_date" />
                </div>

                <div class="col">
                    <label for="end_date">End Date (optional):</label>
                    <input type="date" id="end_date" name="end_date" />
                </div>
            </div>

            <label for="main_img">Main Image Upload (optional):</label>
            <input type="file" id="main_img" name="main_img" accept="image/*" onchange="previewImage(this)" />
            <img id="imgPreview" style="max-width: 200px; max-height: 200px;" alt="Image Preview" />
            <br><br>
            <input type="submit" value="Initialize Itinerary">

        </form>
    </div>
    <script>
        const imgPreview = document.getElementById('imgPreview');
        const mainImgInput = document.getElementById('main_img');

        // Function to show image preview
        function previewImage(input) {
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgPreview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        }
    </script>
</body>

</html>