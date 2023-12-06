<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
include 'layouts/navbar.php';

function displayItinerary($db) {
    // Prepare the SELECT query
    $sql = "SELECT * FROM itinerary";

    $result = $db->query($sql);

    if (!$result) {
        die("Error executing query: " . $db->error);
    }

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<div class='itinerary-card'>";
            echo "<h3>" . $row["trip_name"] . "</h3>";
            echo "<h4>" . $row["trip_location"] . "</h4>";
            echo "<p>" . $row["trip_description"] . "</p>";
            echo "<p><strong>Status:</strong> " . $row["status"] . "</p>";
            echo "<p><strong>Start Date:</strong> " . $row["start_date"] . "</p>";
            echo "<p><strong>End Date:</strong> " . $row["end_date"] . "</p>";
            echo "<p><strong>Duration:</strong> " . $row["duration"] . " days</p>";
            echo "<p><strong>Group Size:</strong> " . $row["group_size"] . "</p>";
            echo "<p>Last updated " . $row["last_updated_date"] . "</p>";

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
        }
    } else {
        echo "No itineraries found.";
    }

    $result->free_result();
}


$db = connectToDatabase(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roamfy</title>
</head>
<body>

    <?php displayItinerary($db); ?>
    
</body>
</html>