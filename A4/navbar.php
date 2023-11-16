<nav>
    <ul>
        <li><a href="showmodels.php">All Models</a></li>
        <li><a href="addtowatchlist.php">Watchlist</a></li>
        <?php
        if (isset($_SESSION['username'])) {
            // User is logged in, show logout link
            echo '<li><a href="logout.php">Logout</a></li>';
        } else {
            // User is not logged in, show login link
            echo '<li><a href="login.php">Login</a></li>';
        }
        ?>
    </ul>
</nav>