<!DOCTYPE html>
<html lang="en">
<body>
    <nav>
        <ul>
            <li><a href="index.php">Roamfy</a></li>
            <li><a href="index.php">Home</a></li>
            <?php
            if (isset($_SESSION['username'])) {
                // User is logged in, show logout link
                echo '<li><a href="create_itinerary.php">Create Itinerary</a></li>';
                echo '<li><a href="logout.php">Logout</a></li>';
                echo '<li><a href="settings.php">Profile Settings</a></li>';
            } else {
                // User is not logged in, show login link
                echo '<li><a href="create_itinerary.php">Create Itinerary</a></li>';
                echo '<li><a href="login.php">Login</a></li>';
            }
            ?>
        </ul>
    </nav>

</body>
</html>