<?php
include 'db_connection.php';
include 'https_redirect.php';

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
            echo "Itinerary ID: " . $row['itinerary_id'] . "<br>";
            echo "Trip Name: " . $row['trip_name'] . "<br>";
            echo "Trip Location: " . $row['trip_location'] . "<br>";

            // Display the base64-encoded image
            $base64Image = $row['main_img'];
            $imageData = base64_encode($base64Image);
            $imageMimeType = 'image/jpg';  // Adjust the MIME type based on your image format (e.g., 'image/png', 'image/jpeg', etc.)

            echo "<p><strong>Main Image:</strong> <img src='data:$imageMimeType;charset=utf8;base64,$imageData' alt='Main Image'></p>";

            // Repeat for other fields...

            echo "<br>";
        }
    } else {
        echo "No itineraries found.";
    }

    $result->free_result();
}

// Call the function to display itinerary
$db = connectToDatabase();  // You need to implement this function
displayItinerary($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roamfy</title>
</head>
<body>


    
</body>
</html>