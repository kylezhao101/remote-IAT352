<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
include 'layouts/navbar.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $itineraryId = $_GET['id'];
} else {
    echo "Invalid itinerary ID.";
}

function displayItineraryDetails($itineraryId)
{
    // Prepare the SELECT query with a JOIN statement
    $sql = "SELECT i.*, m.username 
            FROM itinerary i
            LEFT JOIN member m ON i.member_id = m.member_id
            WHERE i.itinerary_id = ?";

    $db = connectToDatabase();
    $stmt = $db->prepare($sql);

    // Bind the parameter
    $stmt->bind_param("i", $itineraryId);

    // Execute the statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
        // Output data of the specified itinerary
        $row = $result->fetch_assoc();

        // Add link to edit_itinerary.php if user is logged in and owns the itinerary
        if (isset($_SESSION['username']) && $_SESSION['member_id'] == $row['member_id']) {
            echo "<p><a href='edit_itinerary.php?id=" . $row["itinerary_id"] . "'>Edit Your Itinerary</a></p>";
        }
        
        echo "<div class='itinerary-header'>";
        echo "<h1>" . $row["trip_name"] . "</h1>";
        echo "<h4>" . $row["trip_location"] . "</h4>";
        echo "<p>" . $row["trip_description"] . "</p>";
        echo "<p><strong>Status:</strong> " . $row["status"] . "</p>";
        echo "<p><strong>Start Date:</strong> " . $row["start_date"] . "</p>";
        echo "<p><strong>End Date:</strong> " . $row["end_date"] . "</p>";
        echo "<p><strong>Duration:</strong> " . $row["duration"] . " days</p>";
        echo "<p><strong>Group Size:</strong> " . $row["group_size"] . "</p>";

        echo "<p>Last updated " . $row["last_updated_date"] . "</p>";
        // Display the member_id's username
        echo "<p>Created by: " . $row["username"] . "</p>";

        // Check if number_likes is not null and display it
        if ($row["number_likes"] !== null) {
            echo "<p><strong>Number of Likes:</strong> " . $row["number_likes"] . "</p>";
        } else {
            echo "<p>No likes yet, be the first to like!</p>";
        }

        // Like button
        echo "<form action='add_to_watchlist.php' method='post'>";
        echo "<input type='hidden' name='itinerary_id' value='" . $row["itinerary_id"] . "'>";
        echo "<button type='submit'>";
        echo "<span>&#x2665;</span> Like";
        echo "</button>";
        echo "</form>";

        // Check if forked_from is not null and display it
        if ($row["forked_from"] !== null) {
            echo "<p><strong>Forked From:</strong> " . $row["forked_from"] . "</p>";
        }

        if (!empty($row["main_img"])) {
            echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["main_img"]) . "' alt='Main Image'>";
        } else {
            echo "<p><strong>Main Image:</strong> No image available</p>";
        }
        echo "</div>";
    } else {
        echo "No itinerary found for the specified ID.";
    }

    // Close the statement and result set
    $stmt->close();
    $result->free_result();
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
    <?php displayItineraryDetails($itineraryId); ?>

</body>

</html>
