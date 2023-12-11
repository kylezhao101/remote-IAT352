<?php
session_start();
include 'includes/db_connection.php';
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
    <?php include 'layouts/navbar.php'; ?>
    <div class="search-container">
        <h1>Explore Itineraries</h1>
        <div class='yellow-rectangle'></div>
        <h5>Filter by</h5>
        <div class="status-filter">

            <form method="get" action="">

                <input type="radio" id="all" name="status" value="all" <?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'checked' : ''; ?>>
                <label for="all">All</label>

                <input type="radio" id="planning" name="status" value="planning" <?php echo (isset($_GET['status']) && $_GET['status'] == 'planning') ? 'checked' : ''; ?>>
                <label for="planning">Planning</label>

                <input type="radio" id="in_progress" name="status" value="in_progress" <?php echo (isset($_GET['status']) && $_GET['status'] == 'in_progress') ? 'checked' : ''; ?>>
                <label for="in_progress">In Progress</label>

                <input type="radio" id="complete" name="status" value="complete" <?php echo (isset($_GET['status']) && $_GET['status'] == 'complete') ? 'checked' : ''; ?>>
                <label for="complete">Complete</label>

                <input type="submit" value="Apply">
            </form>
        </div>

    </div>
    <div class="itinerary-cards-container">
        <?php
        // Get the status filter from the URL parameters
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : null;

        // If 'All' is selected, set the $statusFilter to null
        if ($statusFilter === 'all') {
            $statusFilter = null;
        }

        displayItineraryCards($db, $statusFilter);
        ?>
    </div>
</body>

</html>