<?php
session_start();
include 'includes/db_connection.php';
include 'includes/display_itinerary_functions.php';
include 'includes/https_redirect.php';
enforceHttp();

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $itineraryId = $_GET['id'];
} else {
    echo "Invalid itinerary ID.";
}

$db = connectToDatabase();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $itineraryId = $_POST['itinerary_id'];

    $newTripName = $_POST["newTripName"];
    $newTripLocation = $_POST["newTripLocation"];
    $newTripDescription = $_POST["newTripDescription"];
    $newStatus = $_POST["newStatus"];
    $newStartDate = !empty($_POST["newStartDate"]) ? $_POST["newStartDate"] : null;
    $newEndDate = !empty($_POST["newEndDate"]) ? $_POST["newEndDate"] : null;
    $newGroupSize = $_POST["newGroupSize"];

    // Check if a new file has been uploaded
    if ($_FILES['new_img']['error'] == 0) {
        // New file uploaded, process and update the database
        $newMainImg = file_get_contents($_FILES['new_img']['tmp_name']);
    } else {
        // No new file uploaded, retrieve the existing image associated with the entry
        $sql = "SELECT main_img FROM itinerary WHERE itinerary_id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $itineraryId);
        $stmt->execute();
        $stmt->bind_result($existingImage);
        $stmt->fetch();
        $stmt->close();

        $newMainImg = $existingImage;
    }

    // Prepare and execute the UPDATE statement
    $stmt = $db->prepare("UPDATE itinerary SET trip_name=?, trip_location=?, trip_description=?, status=?, start_date=?, end_date=?, group_size=?, main_img=?, last_updated_date=NOW() WHERE itinerary_id=?");

    $stmt->bind_param("ssssssisi", $newTripName, $newTripLocation, $newTripDescription, $newStatus, $newStartDate, $newEndDate, $newGroupSize, $newMainImg, $itineraryId);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the edited itinerary page
        header("Location: edit_itinerary.php?id=$itineraryId");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

function editItineraryDetailsHeader($itineraryId)
{
    // Prepare the SELECT query with a JOIN statement
    $sql = "SELECT i.*, m.username 
            FROM itinerary i
            LEFT JOIN member m ON i.member_id = m.member_id
            WHERE i.itinerary_id = ?";

    $db = connectToDatabase();
    $stmt = $db->prepare($sql);

    // Bind the parameter
    $stmt->bind_param("i", $itineraryId);

    // Execute the statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
        // Output data of the specified itinerary
        $row = $result->fetch_assoc();
?>

        <h1><?= $row["trip_name"] ?></h1>
        <div class="yellow-rectangle"></div>
        <div class='itinerary-header'>


            <div class='itinerary-header-content'>
                <h5><?= $row["trip_location"] ?></h5>

                <div class='itinerary-card-details'>
                    <p><strong>Status:</strong> <?= $row["status"] ?> <strong>| Duration:</strong> <?= $row["duration"] ?> days <strong>| Group Size:</strong> <?= $row["group_size"] ?></p>
                </div>

                <p><?= $row["trip_description"] ?></p><br>

                <?php if (!empty($row["start_date"])) : ?>
                    <p>From <?= $row["start_date"] ?> to <?= $row["end_date"] ?></p>
                <?php endif; ?>

                <p><strong>Created by:</strong> <?= $row["username"] ?></p><br>

                <?php if ($row["forked_from"] !== null) : ?>
                    <p><strong>Forked From:</strong> <?= $row["forked_from"] ?></p>
                <?php endif; ?>

                <br>

                <p><small>Last updated <?= $row["last_updated_date"] ?></small></p>
            </div>
            <?php if (!empty($row["main_img"])) : ?>
                <img src='data:image/jpg;charset=utf8;base64,<?= base64_encode($row["main_img"]) ?>' alt='Main Image'>
            <?php endif; ?>
        </div>

        <button id="editHeaderBtn" class='edit-entry-btn'>Edit Header</button>

        <form id="headerForm" method='post' action="" class='edit-header-form' enctype='multipart/form-data' style="display: none;">
            <input type='hidden' name='itinerary_id' value='<?= $row["itinerary_id"] ?>'>

            <label for="newTripName">Trip Name:</label>
            <input type="text" id="newTripName" name="newTripName" value="<?= $row["trip_name"] ?>"><br>

            <label for="newTripLocation">Trip Location:</label>
            <input type="text" id="newTripLocation" name="newTripLocation" value="<?= $row["trip_location"] ?>"><br>

            <label for="newTripDescription">Trip Description:</label>
            <textarea id="newTripDescription" name="newTripDescription"><?= $row["trip_description"] ?></textarea><br>

            <label for="newStatus">Status:</label>
            <select id="newStatus" name="newStatus">
                <option value="planning" <?= ($row["status"] === "planning") ? "selected" : "" ?>>Planning</option>
                <option value="in_progress" <?= ($row["status"] === "in_progress") ? "selected" : "" ?>>In Progress</option>
                <option value="complete" <?= ($row["status"] === "complete") ? "selected" : "" ?>>Complete</option>
            </select><br>

            <label for="newStartDate">Start Date:</label>
            <input type="date" id="newStartDate" name="newStartDate" value="<?= $row["start_date"] ?>"><br>

            <label for="newEndDate">End Date:</label>
            <input type="date" id="newEndDate" name="newEndDate" value="<?= $row["end_date"] ?>"><br>

            <label for="newGroupSize">Group Size:</label>
            <input type="number" id="newGroupSize" name="newGroupSize" value="<?= $row["group_size"] ?>"><br>

            <label for="new_img">New Main Image:</label>
            <input type="file" id="new_img" name="new_img" accept='image/*'><br>

            <button class='update-entry-btn' type='submit'>Save Changes</button>
        </form>

<?php

    } else {
        echo "No itinerary found for the specified ID.";
    }

    // Close the statement and result set
    $stmt->close();
    $result->free_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Itinerary</title>
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>
    <!-- Display the itinerary header -->
    <div class="itinerary-header-container">
        <h5>Editing...</h5>
        <?php editItineraryDetailsHeader($itineraryId); ?>
    </div>

    <!-- Display the entries container with an id -->
    <div id="itinerary-entries-container" class="itinerary-entries-container">
        <!-- Entries will be dynamically added here -->
    </div>

    <div class="itinerary-new-entry-container">
        <h4>Add Your next entry</h4>
        <div class='yellow-rectangle'></div>
        <div class="itinerary-entry-form">
            <h5>New Entry</h5>

            <form id="newEntry" method="post" action="" enctype="multipart/form-data">

                <input type="hidden" name="itineraryId" value="<?php echo $itineraryId; ?>">

                <label for="accommodation">Accommodation:</label>
                <input type="text" name="accommodation"><br>

                <label for="location">Location:</label>
                <?php include 'includes/location_autocomplete.php'; ?>
                <input type="hidden" id="selected_location" name="selected_location" />

                <label for="main_img">Image:</label>
                <input type="file" id="main_img" name="main_img" accept="image/*" /><br>

                <label for="body_text">Body Text:</label>
                <textarea name="body_text" rows="10" placeholder="What are your ideas?" required></textarea><br>

                <button type="submit" class="create-entry-btn">Add Entry</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            $("#editHeaderBtn").click(function() {
                $("#headerForm").toggle();
                // Change the button text based on the form visibility
                var buttonText = ($("#headerForm").is(":visible")) ? "Cancel" : "Edit Header";
                $("#editHeaderBtn").text(buttonText);
            });
            // Function to load entries dynamically
            function loadEntries() {
                $.ajax({
                    type: "GET",
                    url: "includes/load_entries.php?id=" + <?php echo $itineraryId; ?>, // Specify the correct URL
                    success: function(data) {
                        // Update the entries container with the new entries
                        $("#itinerary-entries-container").html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading entries: " + error);
                    },
                });
            }

            // Prevent form submission for the form with id "newEntry"
            $("#newEntry").submit(function(e) {
                e.preventDefault(); // Prevent the default form submission

                // Use AJAX to submit the form data asynchronously
                $.ajax({
                    type: "POST",
                    url: "includes/process_entry.php", // Specify the correct URL for handling new entry submission
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle the success response if needed
                        console.log("New entry submitted successfully");

                        // Clear the form fields if needed
                        $("#newEntry")[0].reset();

                        // Load entries dynamically after form submission
                        loadEntries();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error submitting new entry: " + error);
                    },
                });
            });

            // Initial load of entries
            loadEntries();
        });
    </script>
</body>

</html>