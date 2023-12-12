<?php

require_once 'db_connection.php';
$db = connectToDatabase();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {

    // Check for a valid database connection
    if (!$db) {
        echo 'Database connection error.';
        exit();
    }

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        echo 'Not logged in';
        exit();
    }

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $itinerary_id = $_POST['itinerary_id'];

        // Debug: Echo the itinerary_id
        echo 'itinerary_id: ' . $itinerary_id . '<br>';

        // Start a database transaction
        $db->begin_transaction();

        // Check for any database errors during the transaction start
        if ($db->error) {
            echo 'Database error during transaction start: ' . $db->error;
            exit();
        }

        // Check if the user has already liked the itinerary
        $checkSql = "SELECT * FROM watchlist WHERE member_id = ? AND itinerary_id = ?";
        $checkStmt = $db->prepare($checkSql);

        if (!$checkStmt) {
            echo 'Check statement preparation error: ' . $db->error;
            exit();
        }

        $checkStmt->bind_param('ii', $_SESSION['member_id'], $itinerary_id);
        $checkStmt->execute();

        // Check for errors in the SELECT query
        if ($checkStmt->error) {
            echo 'Check query error: ' . $checkStmt->error;
            exit();
        }

        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            // User has already liked this itinerary. Unliking.
            $deleteSql = "DELETE FROM watchlist WHERE member_id = ? AND itinerary_id = ?";
            $deleteStmt = $db->prepare($deleteSql);

            if (!$deleteStmt) {
                echo 'Delete statement preparation error: ' . $db->error;
                exit();
            }

            $deleteStmt->bind_param('ii', $_SESSION['member_id'], $itinerary_id);
            $deleteStmt->execute();

            // Check for errors in the DELETE query
            if ($deleteStmt->error) {
                echo 'Delete query error: ' . $deleteStmt->error;
                exit();
            }

            // Update the itinerary table to decrement the number_likes
            $updateSql = "UPDATE itinerary SET number_likes = COALESCE(number_likes, 0) - 1 WHERE itinerary_id = ?";
        } else {
            // User has not liked this itinerary yet. Liking.
            // Insert a new record into the watchlist table
            $insertSql = "INSERT INTO watchlist (member_id, itinerary_id) VALUES (?, ?)";
            $updateSql = "UPDATE itinerary SET number_likes = COALESCE(number_likes, 0) + 1 WHERE itinerary_id = ?";
        }

        $updateStmt = $db->prepare($updateSql);

        if (!$updateStmt) {
            echo 'Update statement preparation error: ' . $db->error;
            exit();
        }

        $updateStmt->bind_param('i', $itinerary_id);

        // Check for errors in the UPDATE query
        $updateStmt->execute();

        if ($updateStmt->error) {
            echo 'Update query error: ' . $updateStmt->error;
        } else {
            if ($updateStmt->affected_rows > 0) {
                echo 'The update query was executed successfully and affected ' . $updateStmt->affected_rows . ' rows.';
            } elseif ($updateStmt->affected_rows === 0) {
                echo 'The update query did not affect any rows. This may be because the row already has the value being set or no row matches the condition.';
            } elseif ($updateStmt->affected_rows === -1) {
                echo 'The update query failed to execute properly.';
            }
        }

        // Close the result set before executing a new query
        $updateStmt->close();

        // Commit the transaction
        $db->commit();
        header("Location: testing/index.php");

        // Fetch the new number of likes
        $selectSql = "SELECT number_likes FROM itinerary WHERE itinerary_id = ?";
        $selectStmt = $db->prepare($selectSql);

        if (!$selectStmt) {
            echo 'Select statement preparation error: ' . $db->error;
            exit();
        }

        $selectStmt->bind_param('i', $itinerary_id);
        $selectStmt->execute();

        // Check for errors in the SELECT query
        if ($selectStmt->error) {
            echo 'Select query error: ' . $selectStmt->error;
            exit();
        }

        $result = $selectStmt->get_result();
        $row = $result->fetch_assoc();
        echo 'Number of likes: ' . $row['number_likes'];

        exit();
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();

    // Rollback the transaction in case of an error
    $db->rollback();
    exit();
}

// Additional error handling for silent errors
$error = error_get_last();

if ($error) {
    echo "Silent Error: " . $error['message'];
}

?>
