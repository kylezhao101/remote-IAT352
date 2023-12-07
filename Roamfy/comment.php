<?php

session_start();
include 'includes/https_redirect.php';
include 'includes/db_connection.php';

// Function to check if the user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['username']);
}

// Function to display the comment form
function displayCommentForm() {
    echo '<form method="post" action="post_comment.php">'; 
    echo '<textarea name="comment" rows="4" cols="50" placeholder="Write your comment here"></textarea><br>';
    echo '<input type="submit" value="Post Comment">';
    echo '</form>';
}

// Function to display user's comments
function displayUserComments($pdo) {
    $query = "SELECT * FROM comments WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $_SESSION['username']);
    $stmt->execute();

    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($comments) {
        echo '<h2>Your Comments:</h2>';
        foreach ($comments as $comment) {
            echo '<p>' . $comment['comment'] . '</p>';
        }
    } else {
        echo '<p>No comments yet.</p>';
    }
}

// Main logic
if (isUserLoggedIn()) {
    // User is logged in, display the comment form
    displayCommentForm();

    // Display existing comments from the database
    displayUserComments($pdo);
} else {
    // User is not logged in, display a message or redirect to the login page
    echo '<p>Please log in to post comments.</p>';
    // You can also redirect to the login page using header('Location: login.php');
}


?>