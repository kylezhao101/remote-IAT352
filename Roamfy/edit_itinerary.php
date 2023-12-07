<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
include 'layouts/navbar.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $itineraryId = $_GET['id'];
} else {
    echo "Invalid itinerary ID.";
}

function displayItineraryDetails($itineraryId)
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

        echo "<div class='itinerary-header'>";
        echo "<h1>" . $row["trip_name"] . "</h1>";
        echo "<h4>" . $row["trip_location"] . "</h4>";
        echo "<p>" . $row["trip_description"] . "</p>";
        echo "<p><strong>Status:</strong> " . $row["status"] . "</p>";
        echo "<p><strong>Start Date:</strong> " . $row["start_date"] . "</p>";
        echo "<p><strong>End Date:</strong> " . $row["end_date"] . "</p>";
        echo "<p><strong>Duration:</strong> " . $row["duration"] . " days</p>";
        echo "<p><strong>Group Size:</strong> " . $row["group_size"] . "</p>";

        echo "<p>Last updated " . $row["last_updated_date"] . "</p>";
        // Display the member_id's username
        echo "<p>Created by: " . $row["username"] . "</p>";

        // Add additional fields or buttons as needed for editing

        echo "</div>";
    } else {
        echo "No itinerary found for the specified ID.";
    }

    // Close the statement and result set
    $stmt->close();
    $result->free_result();
}

function displayEntries($itineraryId)
{
    // Fetch entries from the database
    $sql = "SELECT * FROM itinerary_entry WHERE itinerary_id = ? ORDER BY day_of_trip";

    $db = connectToDatabase();
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $itineraryId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
        echo "<div class='itinerary-entries'>";
        echo "<h2>Entries</h2>";

        while ($row = $result->fetch_assoc()) {
            // Display entry details
            echo "<div>";
            echo "<p>Day " . $row['day_of_trip'] . "</p>";
            echo "<p><strong>Accommodation:</strong> " . $row['accommodation'] . "</p>";
            echo "<p><strong>Location:</strong> " . $row['location'] . "</p>";
            echo "<p><strong>Body Text:</strong> " . $row['body_text'] . "</p>";
            echo "</div>";
        }

        echo "</div>";
    } else {
        echo "<p>No entries found.</p>";
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
    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to show/hide the entry form
            function toggleEntryForm() {
                $(".itinerary-entry-form").toggle();
            }

            // Hide the form initially
            $(".itinerary-entry-form").hide();

            // Add a click event to the "Create New Entry" button
            $("#createNewEntryBtn").click(function() {
                toggleEntryForm();
            });
        });
    </script>
</head>

<body>

    <!-- Display the itinerary header -->
    <?php displayItineraryDetails($itineraryId); ?>
    <?php displayEntries($itineraryId); ?>
    <button id="createNewEntryBtn">Create New Entry</button>

    <!-- Display the entry form -->
    <div class="itinerary-entry-form" style="display: none;">
        <h2>New Entry</h2>
        <form method="post" action="process_entry.php" enctype="multipart/form-data">
            <!-- Hidden field to pass itinerary ID -->
            <input type="hidden" name="itineraryId" value="<?php echo $itineraryId; ?>">

            <label for="accommodation">Accommodation:</label>
            <input type="text" name="accommodation"><br>

            <label for="location">Location:</label>
            <?php include 'location_autocomplete.php'; ?>
            <input type="hidden" id="selected_location" name="selected_location" />

            <label for="image">Image:</label>
            <input type="file" id="main_img" name="main_img" accept="image/*" /><br>

            <label for="body_text">Body Text:</label>
            <textarea name="body_text" rows="10" placeholder="What are your ideas?"></textarea><br>

            <button type="submit">Add Entry</button>
        </form>
    </div>
</body>

</html>