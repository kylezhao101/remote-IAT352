<?php
    // helper functions
    function encodeCommas($text){
        return str_replace(',', '#', $text);
    };

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        //form data
        $title = $_POST["title"];
        $description = $_POST["description"];
        $serving = $_POST["serving"];
        $prep_hr = $_POST["prep_hr"];
        $prep_min = $_POST["prep_min"];
        $cook_hr = $_POST["cook_hr"];
        $cook_min = $_POST["cook_min"];

        //$ingredients = $_POST["ingredients"];

        $instructions = $_POST["instructions"];
        $instructions = str_replace(PHP_EOL, '@@@', $instructions);

        $tags = $_POST["tags"];

        //store errors
        $errors = [];
        if (empty($title)) {
            $errors[] = "Recipe Title is required.";
        }

        if (empty($description)) {
            $errors[] = "Description is required.";
        }

        if (empty($serving)) {
            $errors[] = "Serving information is required.";
        }

        if (empty($prep_hr) || empty($prep_min)) {
            $errors[] = "Both Prep Hours and Prep Minutes are required.";
        }

        if (empty($cook_hr) || empty($cook_min)) {
            $errors[] = "Both Cook Hours and Cook Minutes are required.";
        }

        // Check if at least one ingredient is entered
        $ingredientEntered = false;
        for ($i = 1; $i <= 10; $i++) {
            if (
                isset($_POST["iquantity$i"]) &&
                isset($_POST["imeasurement$i"]) &&
                isset($_POST["iingredient$i"])
            ) {
                $quantity = $_POST["iquantity$i"];
                $unit = $_POST["imeasurement$i"];
                $ingredient = $_POST["iingredient$i"];

                // Check if any of the ingredient fields are not empty
                if (!empty($quantity) || !empty($unit) || !empty($ingredient)) {
                    $ingredientEntered = true;
                    break; // Stop checking once at least one ingredient is entered
                }
            }
        }

        if (!$ingredientEntered) {
            $errors[] = "At least one ingredient is required.";
        }

        if (empty($instructions)) {
            $errors[] = "Instructions are required.";
        }

        if (empty($tags)) {
            $errors[] = "Tags are required.";
        }

        //prepare to write to csv
        if(empty($errors)) {
            echo 'no errors';
            // process description
            $description = encodeCommas($description);

            // Process ingredients
            for ($i = 1; $i <= 10; $i++) {
                // Check if ingredient fields are set
                if (isset($_POST["iquantity$i"]) && isset($_POST["imeasurement$i"]) && isset($_POST["iingredient$i"])) {
                    $quantity = $_POST["iquantity$i"];
                    $unit = $_POST["imeasurement$i"];
                    $ingredient = $_POST["iingredient$i"];

                    // Check if any of the ingredient fields are empty
                    if (empty($quantity) || empty($unit) || empty($ingredient)) {
                        $errors[] = "Ingredient $i is incomplete";
                    } else {
                        if (!is_numeric($quantity)) {
                            $errors[] = "Quantity for ingredient $i must be a number.";
                        } else{
                            // Encode the ingredient and store it
                            $ingredients[] = $quantity . '#' . $unit . '#' . $ingredient;
                        }
                    }
                }
            }

            $encodedIngredients = implode('+++', $ingredients);
            $encodedIngredients = trim($encodedIngredients,'"');
            $tags = encodeCommas($tags);
            $instructions = encodeCommas($instructions);

            $lineCSV = [
                uniqid(),
                $title,
                $description,
                $serving,
                $prep_hr,
                $prep_min,
                $cook_hr,
                $cook_min,
                $encodedIngredients,
                $instructions,
                $tags
            ];

            $csvFile = fopen("./recipes/recipes.csv", "a"); //create file if doesnt exist and open for writing

            if ($csvFile) {
                // Write the recipe data to the CSV file
                fputcsv($csvFile, $lineCSV);

                // Close the CSV file
                fclose($csvFile);
                echo '<h1>Recipe Stored Successfully</h1>';
                echo '<p>Your recipe has been successfully stored.</p>';
                echo '<p>Click <a href="details.php?id=' . $lineCSV[0] . '">here</a> to view the entered recipe.</p>';// Redirect to a success page with a link to view the entered recipe
                exit();

            } else {
                $errors[] = "Failed to open the CSV file for writing.";
            }
        } else {
            session_start();
            $_SESSION["form_errors"] = $errors;
            $_SESSION["form_values"] = $_POST;

            header("Location: add-recipe.php");
            exit();
        }
    }
?>