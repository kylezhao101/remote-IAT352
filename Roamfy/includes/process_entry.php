<?php
session_start();
include 'db_connection.php';

$conn = connectToDatabase();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $itineraryId = $_POST["itineraryId"];

    // Retrieve the maximum day_of_trip for the specified itinerary
    $maxDayOfTripQuery = $conn->prepare("SELECT MAX(day_of_trip) AS max_day_of_trip FROM itinerary_entry WHERE itinerary_id = ?");
    $maxDayOfTripQuery->bind_param("i", $itineraryId);
    $maxDayOfTripQuery->execute();
    $maxDayOfTripResult = $maxDayOfTripQuery->get_result();
    $maxDayOfTripRow = $maxDayOfTripResult->fetch_assoc();
    $maxDayOfTrip = $maxDayOfTripRow["max_day_of_trip"] ?? 0;

    // Increment the day_of_trip value
    $dayOfTrip = $maxDayOfTrip + 1;

    // Retrieve other form data
    $accommodation = $_POST["accommodation"];
    $location = $_POST["selected_location"];
    $image = null; // Initialize to null

    // Handle file upload
    if (isset($_FILES['main_img']) && $_FILES['main_img']['error'] === UPLOAD_ERR_OK) {
        $tempName = $_FILES['main_img']['tmp_name'];
        $image = file_get_contents($tempName);
    }

    $bodyText = $_POST["body_text"];

    // Perform database insertion
    $stmt = $conn->prepare("INSERT INTO itinerary_entry (itinerary_id, day_of_trip, accommodation, location, image, body_text) VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("iissss", $itineraryId, $dayOfTrip, $accommodation, $location, $image, $bodyText);
    
    // Execute the statement
    if ($stmt->execute()) {
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>