<?php
session_start();
include 'includes/db_connection.php';
include 'includes/display_itinerary_functions.php';
include 'includes/comment_functions.php';
include 'includes/https_redirect.php';
enforceHttp();

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $itineraryId = $_GET['id'];
} else {
    echo "Invalid itinerary ID.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Itinerary</title>
    <link rel="stylesheet" href="styles/main.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {

            // Add AJAX submission for the form
            $("#commentForm").submit(function(event) {
                // Prevent the default form submission
                event.preventDefault();

                // Get the form data
                var formData = new FormData(this);

                // Use AJAX to submit the form
                $.ajax({
                    type: "POST",
                    url: "includes/process_comment.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function() {
                        // On successful submission, reload the comments
                        loadComments();
                        $("form.comment-form")[0].reset();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error submitting form: " + error);
                    },
                });
            });

            // Function to load comments dynamically
            function loadComments() {
                $.ajax({
                    type: "GET",
                    url: "includes/load_comments.php?id=" + <?php echo $itineraryId; ?>, // Specify the correct URL
                    success: function(data) {
                        // Update the entries container with the new entries
                        $("#itinerary-comments-container").html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading comments: " + error);
                    },
                });
            }
            // Initial load of comments
            loadComments();
        });
    </script>
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>
    <!-- Display the detailed information of the specified itinerary -->
    <div class="itinerary-header-container">
        <?php displayItineraryDetailsHeader($itineraryId); ?>
    </div>
    <div class="itinerary-entries-container">
        <?php displayEntriesNoEdit($itineraryId); ?>
    </div>


    <div class="comment-section">
        <div class="entry-item">
            <h4>Comments</h4>
            
            <?php
            if (isset($_SESSION['username'])) {
                // User is logged in, display the comment form
            ?>
                <form id="commentForm" action='includes/process_comment.php' method='post' class='comment-form'>
                    <!-- Hidden field to pass itinerary ID -->
                    <input type='hidden' name='itineraryId' value='<?php echo $itineraryId; ?>'>
                    <textarea name='comment_text' rows='4' cols='50' placeholder='Write your comment...' required></textarea><br>
                    <input class="edit-entry-btn" type='submit' value='Post Comment'>
                </form>

            <?php
            } else {
                // User is not logged in, display a message or redirect to the login page
            ?>
                <p>Please <a href="login.php">log in</a> to leave a comment.</p>
            <?php
            } ?>
        </div>
    
        <div id="itinerary-comments-container" class="itinerary-comments-container">
            <!-- Comments will be dynamically added here -->
        </div>
    </div>
    <?php include 'layouts/footer.php'; ?>
</body>

</html>