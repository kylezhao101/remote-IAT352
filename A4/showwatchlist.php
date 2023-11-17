<?php 
session_start();

$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "classicmodels";

// Create connection
$conn = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include 'navbar.php';

// If logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Fetch user ID
    $userQuery = "SELECT id FROM users WHERE email = '$username'";
    $userResult = $conn->query($userQuery);

    if ($userResult->num_rows > 0) {
        $userRow = $userResult->fetch_assoc();
        $user_id = $userRow['id'];

        // Retrieve the message from URL parameter
        $message = isset($_GET['message']) ? $_GET['message'] : '';
        
        // watchlist items
        $watchlistQuery = 
        "SELECT watchlist.productCode, products.productName
        FROM watchlist JOIN products ON watchlist.productCode = products.productCode
        WHERE watchlist.user_id = '$user_id'";

        $watchlistResult = $conn->query($watchlistQuery);

        echo "<h2>Your watchlist:</h2>";
        if (!empty($message)) {
            echo "<p>$message</p>";
        }
        if ($watchlistResult->num_rows > 0) {
            echo "<ul>";
            while ($row = $watchlistResult->fetch_assoc()) {
                $model_id = $row['productCode'];
                $model_name = $row['productName'];
                echo "<li><a href='modeldetails.php?model_id=$model_id'>$model_name</a></li>";
            }
            echo "</ul>";
        } else {
            echo "Your watchlist is empty.";
        }
    } else {
        echo "User not found.";
    }
} else {
    // Store callback url into session if not logged in
    $_SESSION['callback_url'] = 'showwatchlist.php';
    header("Location: login.php");
    exit();
}