<!DOCTYPE html>
<html lang="en">
<body>
    <nav>
        <div class="logo">
            <a href="index.php"><h5>>Roamfy</h5></strong></a>
        </div>
        <ul>
            <li><a href="index.php">Browse Itineraries</a></li>
            <?php
            if (isset($_SESSION['username'])) {
                // User is logged in, show logout link
                echo '<li><a href="logout.php">Logout</a></li>';
                echo '<li><a href="settings.php">Profile Settings</a></li>';
                echo '<li><a href="create_itinerary.php"><strong>Create Itinerary</strong></a></li>';
            } else {
                // User is not logged in, show login link
                echo '<li><a href="create_itinerary.php">Create Itinerary</a></li>';
                echo '<li><a href="login.php"><strong>Login</strong></a></li>';
            }
            ?>
        </ul>
    </nav>
    <div class="nav-spacer"></div>
    <script>
        // Get the height of the nav and set it as the height of the nav-spacer
        document.addEventListener("DOMContentLoaded", function() {
            var navHeight = document.querySelector('nav').offsetHeight;
            document.querySelector('.nav-spacer').style.height = navHeight + 'px';
        });
    </script>
</body>
</html>