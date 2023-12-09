<?php
include 'includes/db_connection.php';
include 'includes/display_itinerary_functions.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $itineraryId = $_GET['id'];

    // Display entries
    displayEntries($itineraryId);
} else {
    echo "Invalid itinerary ID.";
}
?>