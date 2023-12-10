<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
include 'layouts/navbar.php';
include 'includes/display_itinerary_functions.php';

$db = connectToDatabase();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roamfy</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <div class="itinerary-cards-container">
        <?php displayItineraryCards($db); ?>
    </div>
</body>

</html>