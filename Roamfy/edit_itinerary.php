<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
include 'layouts/navbar.php';
include 'includes/display_itinerary_functions.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $itineraryId = $_GET['id'];
} else {
    echo "Invalid itinerary ID.";
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
    <script>
        
        $(document).ready(function() {
            
            //hide form initially
            $(".itinerary-entry-form").hide();

            //toggle and change button text on click
            $("#createNewEntryBtn").click(function() {
                $(".itinerary-entry-form").toggle();
                var buttonText = $("#createNewEntryBtn").text();
                var newButtonText = buttonText === "Create New Entry" ? "Cancel" : "Create New Entry";
                $("#createNewEntryBtn").text(newButtonText);
            });

            // Add AJAX submission for the form
            $("form").submit(function(event) {
                // Prevent the default form submission
                event.preventDefault();

                // Get the form data
                var formData = new FormData(this);

                // Use AJAX to submit the form
                $.ajax({
                    type: "POST",
                    url: "includes/process_entry.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        // On successful submission, reload the entries
                        loadEntries();
                        // Hide the entry form again
                        toggleEntryForm();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error submitting form: " + error);
                    },
                });
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

            // Initial load of entries
            loadEntries();
        });
    </script>
</head>

<body>
    <!-- Display the itinerary header -->
    <div class="itinerary-header-container">
    <h5>Editing...</h5>
        <?php displayItineraryDetailsHeader($itineraryId); ?>
    </div>

    <!-- Display the entries container with an id -->
    <div id="itinerary-entries-container" class="itinerary-entries-container">
        <!-- Entries will be dynamically added here -->
    </div>
    <div class="itinerary-new-entry-container">
        <h4>Add Your next entry</h4>
        <div class='yellow-rectangle'></div>
        <button id="createNewEntryBtn" class="create-entry-btn">Create New Entry</button>

        <!-- Display the entry form -->
        <div class="itinerary-entry-form" style="display: none;">
            <h5>New Entry</h5>
            <form method="post" action="includes/process_entry.php" enctype="multipart/form-data">
                <!-- Hidden field to pass itinerary ID -->
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
</body>

</html>