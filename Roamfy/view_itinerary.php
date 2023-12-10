<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
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
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>
    <!-- Display the detailed information of the specified itinerary -->
    <div class="itinerary-header-container">
        <?php displayItineraryDetailsHeader($itineraryId); ?>
    </div>
    <div class="itinerary-entries-container">
        <?php displayEntriesNoEdit($itineraryId); ?>
    </div>

</body>

</html>
