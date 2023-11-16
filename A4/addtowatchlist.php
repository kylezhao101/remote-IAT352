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

if (isset($_SESSION['username'])) {
    $user_id = $_SESSION['username'];
    $model_id = $_POST['model_id']; 

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO watchlist (user_id, model_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $model_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Model added to watchlist successfully.";

        // Display the watchlist
        $result = $conn->query("SELECT * FROM watchlist WHERE user_id = $user_id");

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "model_id: " . $row["model_id"]. "<br>";
            }
        } else {
            echo "0 results";
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    // Store callback url into session
    $_SESSION['callback_url'] = 'addtowatchlist.php';
    // Redirect to login page
    header("Location: login.php");
    exit();
}
?>
