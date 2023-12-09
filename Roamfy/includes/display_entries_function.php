<?php
function displayEntries($itineraryId)
{
    // Fetch entries from the database
    $sql = "SELECT * FROM itinerary_entry WHERE itinerary_id = ? ORDER BY day_of_trip";

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
                    if (!empty($row["main_img"])) {
                        echo "<img src='data:image/jpg;charset=utf8;base64," . base64_encode($row["main_img"]) . "' alt='Image'>";
                    } else {
                        echo "<p><strong>Image:</strong> No image available</p>";
                    }
                    ?>
                    <button class='edit-entry-btn' id='editEntry<?= $row['itinerary_entry_id'] ?>' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>Edit Entry</button>

                    <!-- Edit Entry Form (under each entry) -->
                    <div class='edit-entry-form' id='editEntryForm<?= $row['itinerary_entry_id'] ?>' style='display: none;'>
                        <form method='post' action='includes/process_entry_update.php' enctype='multipart/form-data' data-entry-id='<?= $row['itinerary_entry_id'] ?>'>
                            <!-- Hidden field to pass itinerary ID -->
                            <input type='hidden' name='itineraryId' value='<?= $itineraryId ?>'>
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
                </div>
            <?php
            }
            ?>

            <script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>
            <script>
                $(document).ready(function() {
                    function toggleForm(entryId) {
                        $("#editEntryForm" + entryId).toggle();
                        var buttonText = $("#editEntry" + entryId).text();
                        var newButtonText = buttonText === "Edit Entry" ? "Cancel" : "Edit Entry";
                        $("#editEntry" + entryId).text(newButtonText);
                    }

                    $(".edit-entry-form").hide();

                    $(".edit-entry-btn").click(function() {
                        var entryId = $(this).data('entry-id');
                        toggleForm(entryId);
                    });
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
?>