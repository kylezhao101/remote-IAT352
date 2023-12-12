<?php

// Function to display Itinerary cards ----------------------------------------
function displayItineraryCards($db, $statusFilter = null, $myItineraries = null, $viewLiked = null)
{
    // Prepare the SELECT query with a JOIN statement
    $sql = "SELECT i.*, m.username 
     FROM itinerary i
     LEFT JOIN member m ON i.member_id = m.member_id";

    if ($statusFilter || $myItineraries || $viewLiked) {
        // Add a WHERE clause to filter by status, member_id, and/or liked itineraries
        $sql .= " WHERE ";

        $conditions = [];

        if ($statusFilter) {
            $conditions[] = "i.status = ?";
        }

        if ($myItineraries) {
            $conditions[] = "i.member_id = ?";
        }

        if ($viewLiked) {
            $conditions[] = "i.itinerary_id IN (SELECT itinerary_id FROM watchlist WHERE member_id = ?)";
        }

        $sql .= implode(" AND ", $conditions);
    }

    // Prepare the SQL statement
    $stmt = $db->prepare($sql);

    // Bind parameters if applicable
    if ($statusFilter && $myItineraries && $viewLiked) {
        $stmt->bind_param('ssi', $statusFilter, $_SESSION['member_id'], $_SESSION['member_id']);
    } elseif ($statusFilter && $myItineraries) {
        $stmt->bind_param('si', $statusFilter, $_SESSION['member_id']);
    } elseif ($statusFilter && $viewLiked) {
        $stmt->bind_param('si', $statusFilter, $_SESSION['member_id']);
    } elseif ($myItineraries && $viewLiked) {
        $stmt->bind_param('ii', $_SESSION['member_id'], $_SESSION['member_id']);
    } elseif ($statusFilter) {
        $stmt->bind_param('s', $statusFilter);
    } elseif ($myItineraries) {
        $loggedInMemberId = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : null;
        $stmt->bind_param('i', $loggedInMemberId);
    } elseif ($viewLiked) {
        $stmt->bind_param('i', $_SESSION['member_id']);
    }

    // Execute the query
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

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

            if (!empty($row["start_date"])) {
                echo "<p>From " . $row["start_date"] . " to " . $row["end_date"] . "</p>";
            }
            // Display the member_id's username
            echo "<p><strong>Created by:</strong> " . $row["username"] . "</p><br>";

            // Check if number_likes is not null and display it
            if ($row["number_likes"] !== null && $row["number_likes"] !== 0) {
                echo "<p><strong>Number of Likes:</strong> " . $row["number_likes"] . "</p>";
            } else {
                echo "<p>No likes yet, be the first to like!</p>";
            }

            // Check if forked_from is not null and display it
            if ($row["forked_from"] !== null) {
                echo "<p><strong>Forked From:</strong> " . $row["forked_from"] . "</p>";
            }
            echo "<br>";
            // Add link to edit_itinerary.php if user is logged in and owns the itinerary
            if (isset($_SESSION['username']) && $_SESSION['member_id'] == $row['member_id']) {
                echo "<p><a href='edit_itinerary.php?id=" . $row["itinerary_id"] . "'>Edit Your Itinerary</a></p>";
            }
            echo "<p><small>Last updated " . $row["last_updated_date"] . "</small></p>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No itineraries found.</p>";
    }

    $result->free_result();
    $stmt->close();
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
        // Check if the user is logged in
        $isLoggedIn = isset($_SESSION['username']);

        // Check if the current user has already liked this itinerary
        $isLiked = false;
        if ($isLoggedIn) {
            $watchlistQuery = "SELECT * FROM watchlist WHERE member_id = ? AND itinerary_id = ?";
            $watchlistStmt = $db->prepare($watchlistQuery);
            $watchlistStmt->bind_param("ii", $_SESSION['member_id'], $itineraryId);
            $watchlistStmt->execute();
            $watchlistResult = $watchlistStmt->get_result();
            $isLiked = $watchlistResult->num_rows > 0;
            $watchlistStmt->close();
        }
?>

        <h1><?= $row["trip_name"] ?></h1>
        <div class="yellow-rectangle"></div>
        <div class='itinerary-header'>

            <div class='itinerary-header-content'>
                <h5><?= $row["trip_location"] ?></h5>

                <div class='itinerary-card-details'>
                    <p><strong>Status:</strong> <?= $row["status"] ?> <strong>| Duration:</strong> <?= $row["duration"] ?> days <strong>| Group Size:</strong> <?= $row["group_size"] ?></p>
                </div>

                <p><?= $row["trip_description"] ?></p><br>

                <?php if (!empty($row["start_date"])) : ?>
                    <p>From <?= $row["start_date"] ?> to <?= $row["end_date"] ?></p>
                <?php endif; ?>

                <p><strong>Created by:</strong> <?= $row["username"] ?></p><br>

                <?php if ($row["number_likes"] !== null) : ?>
                    <p><strong>Number of Likes:</strong> <?= $row["number_likes"] ?></p>
                <?php else : ?>
                    <p>No likes yet, be the first to like!</p>
                <?php endif; ?>

                <br>
                <?php if ($isLoggedIn) : ?> <form id="likeForm" action='includes/add_to_watchlist.php' method='post'>
                        <input type='hidden' name='itinerary_id' value='<?= $row["itinerary_id"] ?>'>
                        <button type='submit' class="like-button">
                            <span>&#x2665;</span> <?= $isLiked ? 'Unlike' : 'Like' ?>
                        </button>
                    </form>
                <?php else : ?>
                    <p>Sign in to like this itinerary</p>
                <?php endif; ?>

                <?php if ($row["forked_from"] !== null) : ?>
                    <p><strong>Forked From:</strong> <?= $row["forked_from"] ?></p>
                <?php endif; ?>

                <br>

                <?php if (isset($_SESSION['username']) && $_SESSION['member_id'] == $row['member_id']) : ?>
                    <p><a href='edit_itinerary.php?id=<?= $row["itinerary_id"] ?>'>Edit Your Itinerary</a></p>
                <?php endif; ?>

                <p><small>Last updated <?= $row["last_updated_date"] ?></small></p>
            </div>
            <?php if (!empty($row["main_img"])) : ?>
                <img src='data:image/jpg;charset=utf8;base64,<?= base64_encode($row["main_img"]) ?>' alt='Main Image'>
            <?php endif; ?>
        </div>
    <?php
    } else {
        echo "No itinerary found for the specified ID.";
    }

    // Close the statement and result set
    $stmt->close();
    $result->free_result();
}

function getNumberOfEntries($itineraryId)
{
    $db = connectToDatabase();

    // Prepare the query to get the count of entries
    $sql = "SELECT COUNT(*) AS entry_count FROM itinerary_entry WHERE itinerary_id = ?";

    $stmt = $db->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $db->error);
    }

    // Bind parameters
    $stmt->bind_param("i", $itineraryId);

    // Execute the query
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Fetch the entry count
    $row = $result->fetch_assoc();
    $entryCount = $row['entry_count'];

    // Close statement and connection
    $stmt->close();
    $db->close();

    return $entryCount;
}

// Function to display entries in edit mode ----------------------------------------
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
            <?php
            while ($row = $result->fetch_assoc()) {
                // Display entry details
            ?>
                <div class='itinerary-entry'>
                    <div class='entry-header'>
                        <div class='entry-item'>
                            <h4>Day 0<?= $row['day_of_trip'] ?></h4>
                            <div class='yellow-rectangle'></div>
                        </div>
                        <div>
                            <h6><?= $row['location'] ?></h6>
                        </div>
                    </div>

                    <div class='entry-content'>
                        <?php
                        if (!empty($row["image"])) {
                            echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["image"]) . "' alt='Image'>";
                        }
                        ?>
                        <div class='entry-item'>
                            <p class='entry-body'><?= $row['body_text'] ?></p><br>
                            <p><strong>Accommodation:</strong> <?= $row['accommodation'] ?></p>
                        </div>
                    </div>
                </div>

                <div>
                    <?php
                    if (isset($_SESSION["username"]) && $_SESSION['member_id'] == $row['member_id']) {
                        // Move the "Edit Entry" button here
                    ?>
                        <button class='edit-entry-btn' id='editEntry<?= $row['itinerary_entry_id'] ?>' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>Edit Entry</button>

                        <!-- Edit Entry Form (under each entry) -->
                        <div class='edit-entry-form' id='editEntryForm<?= $row['itinerary_entry_id'] ?>' style='display: none;'>
                            <form id="entryEditForm" enctype='multipart/form-data' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>
                                <!-- Hidden field to pass itinerary entry ID -->
                                <input type='hidden' name='itinerary_entry_id' value='<?= $row['itinerary_entry_id'] ?>'>
                                <label for="move_to_day">Move to day (Does not re-order other entries):</label>

                                <select name="move_to_day" id="move_to_day">
                                    <option value="0" selected>Don't Change</option>

                                    <?php
                                    $totalEntries = getNumberOfEntries($itineraryId);

                                    // Populate dropdown options
                                    for ($day = 1; $day <= $totalEntries; $day++) {
                                        echo "<option value='$day'>$day</option>";
                                    }
                                    ?>
                                </select>

                                <label for='accommodation'>Accommodation:</label>
                                <input type='text' name='accommodation' value='<?= $row['accommodation'] ?>'><br>
                                <!-- location autocomplete needs to be implemented -->
                                <label for='location'>Location:</label>
                                <input type='text' id='selected_location' name='selected_location' value='<?= $row['location'] ?>' />

                                <label for='main_img'>New Image:</label>
                                <input type='file' id='main_img' name='main_img' accept='image/*' /><br>
                                <br>
                                <label for='body_text'>Body Text:</label>
                                <textarea name='body_text' rows='10' placeholder='What are your ideas?'><?= $row['body_text'] ?></textarea><br>

                                <button class='update-entry-btn' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>Update Entry</button>
                            </form>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <button class='delete-entry-btn' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>Delete Entry</button>

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

                    $(".itinerary-entries").on("click", ".edit-entry-btn", function() {
                        var entryId = $(this).data('entry-id');
                        toggleForm(entryId);
                    });

                    // entry deletion
                    $(".itinerary-entries").on("click", ".delete-entry-btn", function() {
                        var entryId = $(this).data('entry-id');
                        console.log('clicked' + entryId)
                        $.ajax({
                            type: "POST",
                            url: "includes/process_entry_delete.php",
                            data: {
                                entry_id: entryId
                            },
                            success: function() {
                                // On successful deletion, reload the entries
                                loadEntries();
                            },
                            error: function(xhr, status, error) {
                                console.error("Error deleting entry: " + error);
                            },
                        });
                    });

                    // Add AJAX submission for the form
                    $("#entryEditForm").submit(function(event) {
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

// Function to display entries ----------------------------------------
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
            <?php
            while ($row = $result->fetch_assoc()) {
                // Display entry details
            ?>
                <div class='itinerary-entry'>
                    <div class='entry-header'>
                        <div class='entry-item'>
                            <h4>Day 0<?= $row['day_of_trip'] ?></h4>
                            <div class='yellow-rectangle'></div>
                        </div>
                        <div>
                            <h6><?= $row['location'] ?></h6>
                        </div>
                    </div>

                    <div class='entry-content'>
                        <?php
                        if (!empty($row["image"])) {
                            echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["image"]) . "' alt='Image'>";
                        }
                        ?>
                        <div class='entry-item'>
                            <p class='entry-body'><?= $row['body_text'] ?></p><br>
                            <p><strong>Accommodation:</strong> <?= $row['accommodation'] ?></p>
                        </div>
                    </div>
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