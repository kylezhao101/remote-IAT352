<?php
// includes/process_entry_update.php
include ("db_connection.php");
$db = connectToDatabase();
var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $entryId = $_POST['itinerary_entry_id'];
    $accommodation = $_POST['accommodation'];
    $location = $_POST['selected_location'];
    $bodyText = $_POST['body_text'];

    // Handle image upload
    $image = null;
    if ($_FILES['main_img']['error'] == 0) {
        $image = file_get_contents($_FILES['main_img']['tmp_name']);
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
?>
