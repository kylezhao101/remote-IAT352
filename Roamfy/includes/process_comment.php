<?php
session_start();
include 'db_connection.php';
$db = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $itineraryId = $_POST['itineraryId'];
    $memberId = $_SESSION['member_id'];
    $commentText = $_POST['comment_text'];

    // Get the current date and time
    $currentDate = date('Y-m-d');
    $currentTime = time();

    // Perform the database insertion
    $sql = "INSERT INTO comment (member_id, body_text, date, time, itinerary_id)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("isssi", $memberId, $commentText, $currentDate, $currentTime, $itineraryId);

    if ($stmt->execute()) {
        exit();
    } else {
        // On failure, you may want to return an error message
        echo "Error submitting comment: " . $stmt->error;
    }

    $stmt->close();
}
$db->close();
?>