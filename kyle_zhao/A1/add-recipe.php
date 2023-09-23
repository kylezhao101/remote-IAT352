<?php
    //get session data
    session_start();
    $formErrors = isset($_SESSION["form_errors"]) ? $_SESSION["form_errors"] : [];
    $formValues = isset($_SESSION["form_values"]) ? $_SESSION["form_values"] : [];

    //clear session data
    unset($_SESSION["form_errors"]);
    unset($_SESSION["form_values"]);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add a New Recipe</title>
        <!-- CSS -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <?php
            include('./util/nav.php');
        ?>
        <h1>Add a New Recipe</h1>
        <?php

        // display errors
        if (!empty($formErrors)) {
            echo '<div style="color: red;">';
            foreach ($formErrors as $error) {
                echo '<p>' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
        ?>

        <form method="post" action='process-recipe.php'>
            <div class="form-text">
                <label for='title'>Recipe Title</label>
                <input type='text' id='title' name='title' placeholder='Title'>

                <label for='description'>Description</label>
                <textarea id='description' name='description' rows='8' placeholder='description'></textarea>
            </div>
            <p>This recipe serves</p>
            <input type='radio' id='1' value='1' name='serving'>
            <label for='1'>1</label>
            <input type='radio' id='2' value='2' name='serving'>
            <label for='2'>2</label>
            <input type='radio' id='3' value='3' name='serving'>
            <label for='3'>3</label>
            <input type='radio' id='4' value='4' name='serving'>
            <label for='4'>4</label>

            <div class="form-time">
                <label for='prep_hr'>Prep time</label>
                <input type='number' id='prep_hr' name='prep_hr' placeholder='hr'>
                <input type='number' id='prep_min' name='prep_min' placeholder='min'>
            </div>
            
            <div class="form-time">
                <label for='cook_hr'>Cook time</label>
                <input type='number' id='cook_hr' name='cook_hr' placeholder='hr'>
                <input type='number' id='cook_min' name='cook_min' placeholder='min'>
            </div>

            <table>
                <tr>
                    <th>Quantity</th>
                    <th>Measurement</th>
                    <th>Ingredient</th>
                </tr>
                <?php
                // Generate 10 rows for ingredients
                for ($i = 1; $i <= 10; $i++) {
                    echo '<tr>';
                    echo '<td><input type="text" name="iquantity' . $i . '" placeholder="Quantity"></td>';
                    echo '<td>';
                    echo '<select name="imeasurement' . $i . '">';
                    echo '<option value="" disabled selected>(none)</option>';
                    echo '<option value="pound(s)">pound(s)</option>';
                    echo '<option value="gram(s)">gram(s)</option>';
                    echo '<option value="ounce(s)">ounce(s)</option>';
                    echo '<option value="pcs">pcs</option>';
                    echo '<option value="ml">ml</option>';
                    echo '<option value="tblspoon">tbl spoon</option>';
                    echo '<option value="teaspoon">teaspoon</option>';
                    echo '<option value="cup">cup</option>';
                    echo '</select>';
                    echo '</td>';
                    echo '<td><input type="text" name="iingredient' . $i . '" placeholder="Ingredient"></td>';
                    echo '</tr>';
                }
                ?>
            </table>
            <div class="form-text">
                <label for="instructions">Instructions (each step on a separate line):</label>
                <textarea id="instructions" name="instructions" rows="15" placeholder="Instructions"></textarea>

                <label for="tags">Tags (separate by commas):</label>
                <input type="text" id="tags" name="tags">
            </div>

            <input type='submit' value='Add Recipe'>
        </form>

        <?php
            include('./util/footer.php');
        ?>
    </body>
</html>