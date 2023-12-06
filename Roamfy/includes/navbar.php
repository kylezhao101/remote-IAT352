<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="explore.php">Explore</a></li>
        <li><a href="create_itinerary.php">Create create_itinerary</a></li>
        <li><a href="profile.php">Home</a></li>
        <li><a href="showwatchlist.php">Watchlist</a></li>
        <li id="searchContainer">
                <input type="text" id="searchInput" placeholder="Search...">
                <button id="filterButton" onclick="applyFilter()">Filter</button>
            </li>
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