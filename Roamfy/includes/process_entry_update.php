<?php
// includes/process_entry_update.php
include("db_connection.php");
$db = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $entryId = $_POST['itinerary_entry_id'];
    $accommodation = $_POST['accommodation'];
    $location = $_POST['selected_location'];
    $bodyText = $_POST['body_text'];

    // Check if a new file has been uploaded
    if ($_FILES['main_img']['error'] == 0) {
        // New file uploaded, process and update the database
        $image = file_get_contents($_FILES['main_img']['tmp_name']);
    } else {
        // No new file uploaded, retrieve the existing image associated with the entry
        $sql = "SELECT image FROM itinerary_entry WHERE itinerary_entry_id=?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $entryId);
        $stmt->execute();
        $stmt->bind_result($existingImage);
        $stmt->fetch();
        $stmt->close();

        $image = $existingImage;
    }

    $moveToDay = $_POST['move_to_day'];
    // Handle "move to day" functionality
    if ($moveToDay !== '0') {
        // Update the day_of_trip column
        $sqlUpdateDay = "UPDATE itinerary_entry SET day_of_trip=? WHERE itinerary_entry_id=?";
        $stmtUpdateDay = $db->prepare($sqlUpdateDay);
        $stmtUpdateDay->bind_param("ii", $moveToDay, $entryId);

        if ($stmtUpdateDay->execute()) {
            echo "Update day_of_trip successful!";
        } else {
            echo "Update day_of_trip failed: " . $stmtUpdateDay->error;
        }

        $stmtUpdateDay->close();
    }

    // Perform the database update
    $sql = "UPDATE itinerary_entry SET accommodation=?, location=?, body_text=?, image=? WHERE itinerary_entry_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssssi", $accommodation, $location, $bodyText, $image, $entryId);

    if ($stmt->execute()) {
        echo "Update successful!";
    } else {
        echo "Update failed: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Handle non-POST requests
    echo "Invalid request method.";
}

// Close the database connection
$db->close();
