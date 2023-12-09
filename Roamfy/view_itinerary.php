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
    <title>View Itinerary</title>
</head>

<body>

    <!-- Display the detailed information of the specified itinerary -->
    <?php displayItineraryDetailsHeader($itineraryId); ?>
    
    <?php displayEntriesNoEdit($itineraryId); ?>

</body>

</html>
