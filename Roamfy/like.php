<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';

// Check if member is logged in
function isMemberLoggedIn() {
    return isset($_SESSION['member_id']);
}

// Function to update number of likes in the itinerary table
function updateLikes($itineraryId) {
    global $conn; // Assuming $conn is your database connection variable

    // Update the number of likes in the database
    $query = "UPDATE itinerary SET number_likes = number_likes + 1 WHERE itinerary_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $itineraryId);
    
    if ($stmt->execute()) {
        return true; // Update successful
    } else {
        return false; // Update failed
    }
}

// Check if the like button is clicked
if (isset($_POST['like_button'])) {
    // Ensure member is logged in before allowing them to like
    if (isMemberLoggedIn()) {
        $itineraryId = $_POST['itinerary_id']; // Assuming you have a form field for itinerary_id

        // Call the function to update the number of likes
        if (updateLikes($itineraryId)) {
            echo "Like successful!";
        } else {
            echo "Failed to update likes.";
        }
    } else {
        echo "You must be logged in to like.";
    }
}
?>




