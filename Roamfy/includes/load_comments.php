<?php
include 'db_connection.php';
include 'comment_functions.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $itineraryId = $_GET['id'];

    // Display comments
    displayComments($itineraryId);
} else {
    echo "Invalid itinerary ID.";
}
?>