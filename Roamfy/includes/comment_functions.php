<?php

function displayComments($itineraryId)
{
    
    // Fetch comments here
    $sql = "SELECT c.*, m.username
        FROM comment c
        JOIN member m ON c.member_id = m.member_id
        JOIN itinerary i ON c.itinerary_id = i.itinerary_id
        WHERE c.itinerary_id = ?
        ORDER BY c.date DESC, c.time DESC";

    $db = connectToDatabase();
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $itineraryId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        ?>
        <div class='itinerary-comments' id='itinerary-comments-container'>
            <?php
            while ($row = $result->fetch_assoc()) {
                // Render each comment
                ?>
                <div class='comment'>
                    <p><strong><?php echo $row['username']; ?>:</strong> <?php echo $row['body_text']; ?></p>
                    <p><small>Posted: <?php echo $row['date']; ?></small></p>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    } else {
        echo "<p>No Comments found.</p>";
    }
}
?>
