<?php
session_start();

// Database connection
$mysqli = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

// Get the model_id from the request (you'll need to replace this with your actual model_id)
$model_id = $_REQUEST['model_id'];

// Insert the new watchlist item into the database
$stmt = $mysqli->prepare("INSERT INTO watchlist (user_id, model_id) VALUES (?, ?)");
$stmt->bind_param("ii", $_SESSION['user_id'], $model_id);

if ($stmt->execute()) {
    echo "Model added to watchlist successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Display the updated watchlist
$result = $mysqli->query("SELECT * FROM watchlist WHERE user_id = " . $_SESSION['user_id']);

while ($row = $result->fetch_assoc()) {
    echo "<a href='modeldetails.php?id=" . $row['model_id'] . "'>" . $row['model_id'] . "</a><br>";
}

$stmt->close();
$mysqli->close();
?>
