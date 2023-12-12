<?php
session_start();
include 'includes/db_connection.php';
include 'includes/display_itinerary_functions.php';
include 'includes/https_redirect.php';
enforceHttp();

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
        <form>
            <div class="status-filter">



                <input type="radio" id="all" name="status" value="all" <?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'checked' : ''; ?>>
                <label for="all">All</label>

                <input type="radio" id="planning" name="status" value="planning" <?php echo (isset($_GET['status']) && $_GET['status'] == 'planning') ? 'checked' : ''; ?>>
                <label for="planning">Planning</label>

                <input type="radio" id="in_progress" name="status" value="in_progress" <?php echo (isset($_GET['status']) && $_GET['status'] == 'in_progress') ? 'checked' : ''; ?>>
                <label for="in_progress">In Progress</label>

                <input type="radio" id="complete" name="status" value="complete" <?php echo (isset($_GET['status']) && $_GET['status'] == 'complete') ? 'checked' : ''; ?>>
                <label for="complete">Complete</label>

                <?php if (isset($_SESSION['username'])) : ?>
                    <!-- Only render if the user is logged in -->
                    <div class="checkbox-container">
                        <label class="checkbox-label" for="myItineraries">Only View My Itineraries</label>
                        <input class="checkbox-input" type="checkbox" id="myItineraries" name="myItineraries" <?php echo (isset($_GET['myItineraries']) && $_GET['myItineraries'] == 'on') ? 'checked' : ''; ?>>
                       
                        <label class="checkbox-label" for="viewLiked">View Liked</label>
                        <input class="checkbox-input" type="checkbox" id="viewLiked" name="viewLiked" <?php echo (isset($_GET['viewLiked']) && $_GET['viewLiked'] == 'on') ? 'checked' : ''; ?>>

                    </div>
                <?php endif; ?>

                <input type="submit" value="Apply">
            </div>
        </form>

    </div>
    <div class="itinerary-cards-container">
        <?php
        // Get the status filter from the URL parameters
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : null;
        $myItineraries = isset($_GET['myItineraries']) && $_GET['myItineraries'] == 'on';
        $viewLiked = isset($_GET['viewLiked']) && $_GET['viewLiked'] == 'on';

        // If 'All' is selected, set the $statusFilter to null
        if ($statusFilter === 'all') {
            $statusFilter = null;
        }

        displayItineraryCards($db, $statusFilter, $myItineraries, $viewLiked);
        ?>
    </div>
    <?php include 'layouts/footer.php'; ?>
</body>

</html>