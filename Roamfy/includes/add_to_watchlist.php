<?php

require_once 'db_connection.php';

$db = connectToDatabase();

if (session_status() == PHP_SESSION_NONE) {
  session_start(); 
}

if (!$db) {
  echo 'Database connection error.';
  exit();
}

if (!isset($_SESSION['username'])) {
  echo 'Not logged in';
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $itinerary_id = $_POST['itinerary_id'];

  // Check if user has liked 
  $checkSql = "SELECT * FROM watchlist WHERE member_id = ? AND itinerary_id = ?";
  
  $checkStmt = $db->prepare($checkSql);
  $checkStmt->bind_param('ii', $_SESSION['member_id'], $itinerary_id);
  $checkStmt->execute();

  if ($checkStmt->error) {
    // Handle error
  } else {

    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) { 
      // Unliking logic
      $deleteSql = "DELETE FROM watchlist WHERE member_id = ? AND itinerary_id = ?";
    
      $deleteStmt = $db->prepare($deleteSql);
      $deleteStmt->bind_param('ii', $_SESSION['member_id'], $itinerary_id);
    
      if($deleteStmt->execute()) {

        $updateSql = "UPDATE itinerary SET number_likes = COALESCE(number_likes, 0) - 1 WHERE itinerary_id = ?";
        
        $updateStmt = $db->prepare($updateSql); 
        $updateStmt->bind_param('i', $itinerary_id);
      
        if($updateStmt->execute()) {
          // Dynamically generate the redirect URL without /includes/
          $redirectURL = str_replace('/includes/', '/', $_SERVER['HTTP_REFERER']);
          header("Location: $redirectURL");
          exit();
        } 
        
      }

    } else {  

      // Liking logic
      $insertSql = "INSERT INTO watchlist (member_id, itinerary_id) VALUES (?, ?)";

      $insertStmt = $db->prepare($insertSql);
      $insertStmt->bind_param('ii', $_SESSION['member_id'], $itinerary_id);

      if($insertStmt->execute()) {
        
        $updateSql = "UPDATE itinerary SET number_likes = COALESCE(number_likes, 0) + 1 WHERE itinerary_id = ?";
        
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->bind_param('i', $itinerary_id);
        
        if($updateStmt->execute()) {
          // Dynamically generate the redirect URL without /includes/
          $redirectURL = str_replace('/includes/', '/', $_SERVER['HTTP_REFERER']);
          header("Location: $redirectURL");
          exit();
        }
      }

    }

  }

}  

?>
