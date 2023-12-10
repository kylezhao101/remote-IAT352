<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<?php

// Function to display Itinerary cards ----------------------------------------
// TODO: implement filtering and search
function displayItineraryCards($db)
{
    // Prepare the SELECT query with a JOIN statement
    $sql = "SELECT i.*, m.username 
            FROM itinerary i
            LEFT JOIN member m ON i.member_id = m.member_id";

    $result = $db->query($sql);

    if (!$result) {
        die("Error executing query: " . $db->error);
    }

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<div class='itinerary-card'>";
            if (!empty($row["main_img"])) {
                echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["main_img"]) . "' alt='Main Image'>";
            } 
            echo "<div class='itinerary-card-content'>";
            // Make the trip_name an anchor linking to view_itinerary.php
            echo "<h4><a href='view_itinerary.php?id=" . $row["itinerary_id"] . "' class='itinerary-link'>" . $row["trip_name"] . "</a></h4>";

            echo "<h5>" . $row["trip_location"] . "</h5>";
            
            echo "<div class='itinerary-card-details'>";
            echo "<p><strong>Status:</strong> " . $row["status"] . "<strong> | Duration:</strong> " . $row["duration"] . " days <strong>| Group Size:</strong> " . $row["group_size"] . "</p>";
            echo "</div>";

            echo "<p>" . $row["trip_description"] . "</p><br>";
            
            if(!empty($row["start_date"])){
                echo "<p>From " . $row["start_date"] . " to " . $row["end_date"] . "</p>";
            }
            // Display the member_id's username
            echo "<p><strong>Created by:</strong> " . $row["username"] . "</p><br>";

            // Check if number_likes is not null and display it
            if ($row["number_likes"] !== null) {
                echo "<p><strong>Number of Likes:</strong> " . $row["number_likes"] . "</p>";
            } else {
                echo "<p>No likes yet, be the first to like!</p>";
            }

            // Like button
            echo "<form action='add_to_watchlist.php' method='post'>";
            echo "<input type='hidden' name='itinerary_id' value='" . $row["itinerary_id"] . "'>";
            echo "<button type='submit'>";
            echo "<span>&#x2665;</span> Like";
            echo "</button>";
            echo "</form>";

            // Check if forked_from is not null and display it
            if ($row["forked_from"] !== null) {
                echo "<p><strong>Forked From:</strong> " . $row["forked_from"] . "</p>";
            }
            // Add link to edit_itinerary.php if user is logged in and owns the itinerary
            if (isset($_SESSION['username']) && $_SESSION['member_id'] == $row['member_id']) {
                echo "<p><a href='edit_itinerary.php?id=" . $row["itinerary_id"] . "'>Edit Your Itinerary</a></p>";
            }
            echo "<p><small>Last updated " . $row["last_updated_date"] . "</small></p>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "No itineraries found.";
    }

    $result->free_result();
}

// Function to display Itinerary headers ----------------------------------------
function displayItineraryDetailsHeader($itineraryId)
{
    // Prepare the SELECT query with a JOIN statement
    $sql = "SELECT i.*, m.username 
            FROM itinerary i
            LEFT JOIN member m ON i.member_id = m.member_id
            WHERE i.itinerary_id = ?";

    $db = connectToDatabase();
    $stmt = $db->prepare($sql);

    // Bind the parameter
    $stmt->bind_param("i", $itineraryId);

    // Execute the statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
        // Output data of the specified itinerary
        $row = $result->fetch_assoc();

        echo "<div class='itinerary-header'>";
        echo "<h1>" . $row["trip_name"] . "</h1>";
        // Add link to edit_itinerary.php if user is logged in and owns the itinerary
        if (isset($_SESSION['username']) && $_SESSION['member_id'] == $row['member_id']) {
            echo "<p><a href='edit_itinerary.php?id=" . $row["itinerary_id"] . "'>Edit Your Itinerary</a></p>";
        }
        echo "<h4>" . $row["trip_location"] . "</h4>";
        echo "<p>" . $row["trip_description"] . "</p>";
        echo "<p><strong>Status:</strong> " . $row["status"] . "</p>";
        echo "<p><strong>Start Date:</strong> " . $row["start_date"] . "</p>";
        echo "<p><strong>End Date:</strong> " . $row["end_date"] . "</p>";
        echo "<p><strong>Duration:</strong> " . $row["duration"] . " days</p>";
        echo "<p><strong>Group Size:</strong> " . $row["group_size"] . "</p>";

        echo "<p>Last updated " . $row["last_updated_date"] . "</p>";
        // Display the member_id's username
        echo "<p>Created by: " . $row["username"] . "</p>";

        // Check if number_likes is not null and display it
        if ($row["number_likes"] !== null) {
            echo "<p><strong>Number of Likes:</strong> " . $row["number_likes"] . "</p>";
        } else {
            echo "<p>No likes yet, be the first to like!</p>";
        }

        // Like button
        echo "<form action='add_to_watchlist.php' method='post'>";
        echo "<input type='hidden' name='itinerary_id' value='" . $row["itinerary_id"] . "'>";
        echo "<button type='submit'>";
        echo "<span>&#x2665;</span> Like";
        echo "</button>";
        echo "</form>";

        // Check if forked_from is not null and display it
        if ($row["forked_from"] !== null) {
            echo "<p><strong>Forked From:</strong> " . $row["forked_from"] . "</p>";
        }

        if (!empty($row["main_img"])) {
            echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["main_img"]) . "' alt='Main Image'>";
        } else {
            echo "<p><strong>Main Image:</strong> No image available</p>";
        }
        echo "</div>";
    } else {
        echo "No itinerary found for the specified ID.";
    }

    // Close the statement and result set
    $stmt->close();
    $result->free_result();
}

// Function to display entries ----------------------------------------
function displayEntries($itineraryId)
{
    session_start();

    // Fetch entries from the database
    $sql = "SELECT ie.*, i.member_id
                FROM itinerary_entry ie
                JOIN itinerary i ON ie.itinerary_id = i.itinerary_id
                WHERE ie.itinerary_id = ? 
                ORDER BY ie.day_of_trip";

    $db = connectToDatabase();
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $itineraryId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
?>
        <div class='itinerary-entries'>
            <h2>Entries</h2>

            <?php
            while ($row = $result->fetch_assoc()) {
                // Display entry details
            ?>
                <div>
                    <p>Day <?= $row['day_of_trip'] ?></p>
                    <p><strong>Accommodation:</strong> <?= $row['accommodation'] ?></p>
                    <p><strong>Location:</strong> <?= $row['location'] ?></p>
                    <p><strong>Body Text:</strong> <?= $row['body_text'] ?></p>
                    <?php
                    if (!empty($row["image"])) {
                        echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["image"]) . "' alt='Image'>";
                    } else {
                        echo "<p><strong>Image:</strong> No image available</p>";
                    }

                    if (isset($_SESSION["username"]) && $_SESSION['member_id'] == $row['member_id']) {
                        // Move the "Edit Entry" button here
                    ?>
                        <button class='edit-entry-btn' id='editEntry<?= $row['itinerary_entry_id'] ?>' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>Edit Entry</button>

                        <!-- Edit Entry Form (under each entry) -->
                        <div class='edit-entry-form' id='editEntryForm<?= $row['itinerary_entry_id'] ?>' style='display: none;'>
                            <form method='post' action='includes/process_entry_update.php' enctype='multipart/form-data' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>
                                <!-- Hidden field to pass itinerary entry ID -->
                                <input type='hidden' name='itinerary_entry_id' value='<?= $row['itinerary_entry_id'] ?>'>

                                <label for='accommodation'>Accommodation:</label>
                                <input type='text' name='accommodation' value='<?= $row['accommodation'] ?>'><br>
                                <!-- location autocomplete needs to be implemented -->
                                <label for='location'>Location:</label>
                                <input type='text' id='selected_location' name='selected_location' value='<?= $row['location'] ?>' />
                                <label for='main_img'>Image:</label>
                                <input type='file' id='main_img' name='main_img' accept='image/*' /><br>
                                <label for='body_text'>Body Text:</label>
                                <textarea name='body_text' rows='10' placeholder='What are your ideas?'><?= $row['body_text'] ?></textarea><br>
                                <button class='update-entry-btn' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>Update Entry</button>
                            </form>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>

            <script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>
            <script>
                $(document).ready(function() {
                    //toggle and change button text on click
                    function toggleForm(entryId) {
                        $("#editEntryForm" + entryId).toggle();
                        var buttonText = $("#editEntry" + entryId).text();
                        var newButtonText = buttonText === "Edit Entry" ? "Cancel" : "Edit Entry";
                        $("#editEntry" + entryId).text(newButtonText);
                    }
                    //hide form initially
                    $(".edit-entry-form").hide();

                    $(".edit-entry-btn").click(function() {
                        var entryId = $(this).data('entry-id');
                        toggleForm(entryId);
                    });

                    // Add AJAX submission for the form
                    $("form").submit(function(event) {
                        // Prevent the default form submission
                        event.preventDefault();

                        // Get the form data
                        var formData = new FormData(this);

                        // Use AJAX to submit the form
                        $.ajax({
                            type: "POST",
                            url: "includes/process_entry_update.php",
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function() {
                                // On successful submission, reload the entries
                                loadEntries();
                                // Hide the entry form again
                                toggleForm();
                            },
                            error: function(xhr, status, error) {
                                console.error("Error submitting form: " + error);
                            },
                        });
                    });
                    // Function to load entries dynamically
                    function loadEntries() {
                        $.ajax({
                            type: "GET",
                            url: "includes/load_entries.php?id=" + <?php echo $itineraryId; ?>, // Specify the correct URL
                            success: function(data) {
                                // Update the entries container with the new entries
                                $("#itinerary-entries-container").html(data);
                            },
                            error: function(xhr, status, error) {
                                console.error("Error loading entries: " + error);
                            },
                        });
                    }
                });
            </script>
        </div>
    <?php
    } else {
        echo "<p>No entries found.</p>";
    }

    // Close the statement and result set
    $stmt->close();
    $result->free_result();
}

// Function to display entries edit mode ----------------------------------------
function displayEntriesNoEdit($itineraryId)
{
    // Fetch entries from the database
    $sql = "SELECT ie.*
            FROM itinerary_entry ie
            WHERE ie.itinerary_id = ? 
            ORDER BY ie.day_of_trip";

    $db = connectToDatabase();
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $itineraryId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are rows in the result set
    if ($result->num_rows > 0) {
    ?>
        <div class='itinerary-entries'>
            <h2>Entries</h2>

            <?php
            while ($row = $result->fetch_assoc()) {
                // Display entry details
            ?>
                <div>
                    <p>Day <?= $row['day_of_trip'] ?></p>
                    <p><strong>Accommodation:</strong> <?= $row['accommodation'] ?></p>
                    <p><strong>Location:</strong> <?= $row['location'] ?></p>
                    <p><strong>Body Text:</strong> <?= $row['body_text'] ?></p>
                    <?php
                    if (!empty($row["image"])) {
                        echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["image"]) . "' alt='Image'>";
                    } else {
                        echo "<p><strong>Image:</strong> No image available</p>";
                    }
                    ?>
                </div>
            <?php
            }
            ?>
        </div>
<?php
    } else {
        echo "<p>No entries found.</p>";
    }

    // Close the statement and result set
    $stmt->close();
    $result->free_result();
}
?>