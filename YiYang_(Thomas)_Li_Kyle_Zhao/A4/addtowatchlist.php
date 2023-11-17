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

// If logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Fetch user ID
    $userQuery = "SELECT id FROM users WHERE email = '$username'";
    $userResult = $conn->query($userQuery);

    if ($userResult->num_rows > 0) {
        $userRow = $userResult->fetch_assoc();
        $user_id = $userRow['id'];

        $model_id = $_GET['model_id'];
        echo "User ID: $user_id, Model ID: $model_id";

        // Check if the model is already in the watchlist
        $checkQuery = "SELECT * FROM watchlist WHERE user_id = '$user_id' AND productCode = '$model_id'";
        $checkResult = $conn->query($checkQuery);
        
        if ($checkResult->num_rows > 0) {
            $_SESSION['message'] = "Model is already in the watchlist.";
            header("Location: showwatchlist.php?message=" . urlencode($_SESSION['message']));
            exit();
        } else {
            // SQL statement
            $stmt = $conn->prepare("INSERT INTO watchlist (user_id, productCode) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $model_id);

            // Execute sQL Statement
            if ($stmt->execute()) {
                $_SESSION['message'] = "Model added to watchlist successfully.";
                header("Location: showwatchlist.php?message=" . urlencode($_SESSION['message']));
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        echo "User not found.";
    }
} else {
    // Store callback url into session if not logged in
    $_SESSION['callback_url'] = 'addtowatchlist.php';
    // Redirect to login page
    header("Location: login.php");
    exit();
}
?>
