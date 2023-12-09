<?php
function displayEntries($itineraryId)
{
    // Fetch entries from the database
    $sql = "SELECT * FROM itinerary_entry WHERE itinerary_id = ? ORDER BY day_of_trip";

    $db = connectToDatabase();
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $itineraryId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
        echo "<div class='itinerary-entries'>";
        echo "<h2>Entries</h2>";

        while ($row = $result->fetch_assoc()) {
            // Display entry details
            echo "<div>";
            echo "<p>Day " . $row['day_of_trip'] . "</p>";
            echo "<p><strong>Accommodation:</strong> " . $row['accommodation'] . "</p>";
            echo "<p><strong>Location:</strong> " . $row['location'] . "</p>";
            echo "<p><strong>Body Text:</strong> " . $row['body_text'] . "</p>";
            if (!empty($row["image"])) {
                echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["image"]) . "' alt='Image'>";
            } else {
                echo "<p><strong>Main Image:</strong> No image available</p>";
            }
            echo "</div>";
        }

        echo "</div>";
    } else {
        echo "<p>No entries found.</p>";
    }

    // Close the statement and result set
    $stmt->close();
    $result->free_result();
}
?>
