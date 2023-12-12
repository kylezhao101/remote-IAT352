<?php
session_start();
include 'db_connection.php';

$db = connectToDatabase();

// Check if the itineraryId is set and not empty
if (isset($_POST['itineraryId']) && !empty($_POST['itineraryId'])) {
    $itineraryId = $_POST['itineraryId'];

    // delete itinerary entries first
    $deleteEntriesQuery = "DELETE FROM itinerary_entry WHERE itinerary_id = ?";
    $stmtEntries = $db->prepare($deleteEntriesQuery);
    $stmtEntries->bind_param("i", $itineraryId);

    if ($stmtEntries->execute()) {
        $stmtEntries->close();

        //delete the itinerary
        $deleteItineraryQuery = "DELETE FROM itinerary WHERE itinerary_id = ?";
        $stmtItinerary = $db->prepare($deleteItineraryQuery);
        $stmtItinerary->bind_param("i", $itineraryId);

        if ($stmtItinerary->execute()) {
            $stmtItinerary->close();
            echo "success";
        } else {
            echo "Error deleting itinerary: " . $stmtItinerary->error;
        }
    } else {
        echo "Error deleting itinerary entries: " . $stmtEntries->error;
    }
} else {
    echo "Invalid itinerary ID.";
}

// Close the database connection
$db->close();
?>
