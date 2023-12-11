<?php
// includes/process_entry_delete.php
include("db_connection.php");
$db = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve entry ID from the POST data
    $entryId = $_POST['entry_id'];

    // Perform the database deletion
    $sql = "DELETE FROM itinerary_entry WHERE itinerary_entry_id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $entryId);

    if ($stmt->execute()) {
        echo "Delete successful!";
    } else {
        echo "Delete failed: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    // Handle non-POST requests
    echo "Invalid request method.";
}

// Close the database connection
$db->close();
?>
