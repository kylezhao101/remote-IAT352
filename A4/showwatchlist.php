<?php 
// Display the watchlist
$result = $conn->query("SELECT * FROM watchlist WHERE user_id = $user_id");

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "model_id: " . $row["model_id"]. "<br>";
    }
} else {
    echo "0 results";
}
?>