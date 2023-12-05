<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Itinerary</title>
    <script src="https://api.geoapify.com/v1/autocomplete?apiKey=API_KEY" defer></script>
</head>
<body>
    <h2>Create Itinerary</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <label for="trip_name">Trip Name:</label>
        <input type="text" id="trip_name" name="trip_name" placeholder="Enter trip name" required />
        
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" placeholder="Enter a location" />

        <label for="trip_description">Trip Description:</label>
        <textarea id="trip_description" name="trip_description" placeholder="Enter trip description" required></textarea>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="planning">Planning</option>
            <option value="in_progress">In Progress</option>
            <option value="complete">Complete</option>
        </select>

        <label for="duration">Duration (days):</label>
        <input type="number" id="duration" name="duration" placeholder="Enter duration" required />

        <label for="group_size">Group Size:</label>
        <input type="number" id="group_size" name="group_size" placeholder="Enter group size"/>

        <label for="main_img">Main Image Upload:</label>
        <input type="file" id="main_img" name="main_img" accept="image/*" onchange="previewImage(this)" />
        <img id="imgPreview" style="max-width: 200px; max-height: 200px;" alt="Image Preview" />

        <input type="submit" value="Create Itinerary">

    </form>

    <script>
        const imgPreview = document.getElementById('imgPreview');
        const mainImgInput = document.getElementById('main_img');

        // Function to show image preview
        function previewImage(input) {
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>