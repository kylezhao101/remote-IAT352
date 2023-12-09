<?php
require_once 'includes/db_connection.php';


class LikeButton
{
    function isMemberLoggedIn()
    {
        return isset($_SESSION['member_id']);
    }

    function hasUserLiked($itineraryId, $conn)
    {
        if ($this->isMemberLoggedIn()) {
            $memberId = $_SESSION['member_id'];
            $query = "SELECT * FROM likes WHERE member_id = ? AND itinerary_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $memberId, $itineraryId);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->num_rows > 0;
        }

        return false;
    }

    function updateLikeCount($itineraryId, $conn)
    {
        $query = "UPDATE itinerary SET number_likes = number_likes + 1 WHERE itinerary_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $itineraryId);
        $stmt->execute();
    }

    function renderLikeButton($itineraryId, $conn)
    {
        echo "<form action='your_like_handler.php' method='post'>";
        echo "<input type='hidden' name='itinerary_id' value='" . $itineraryId . "'>";

        if ($this->isMemberLoggedIn()) {
            echo "<button type='submit' name='like_button'>";
            echo "<span>&#x2665;</span> ";

            if ($this->hasUserLiked($itineraryId, $conn)) {
                echo "Liked";
            } else {
                echo "Like";
            }

            echo "</button>";
        } else {
            echo "<button type='button' disabled>";
            echo "<span>&#x2665;</span> Like (Log in to like)";
            echo "</button>";
        }

        echo "</form>";
    }

    function processLike($itineraryId, $conn)
    {
        if ($this->isMemberLoggedIn() && !$this->hasUserLiked($itineraryId, $conn)) {
            $this->updateLikeCount($itineraryId, $conn);

            // Insert a record in the likes table to keep track of who liked the itinerary
            $memberId = $_SESSION['member_id'];
            $query = "INSERT INTO likes (member_id, itinerary_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $memberId, $itineraryId);
            $stmt->execute();
        }
    }
}

?>




