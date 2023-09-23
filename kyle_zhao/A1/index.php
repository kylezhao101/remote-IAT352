<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>All Recipes</title>
        <!-- CSS -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <?php
            // decoding helper function
            function decodeCommas($text){
                return str_replace('#', ',', $text);
            };

            include('./util/nav.php');

            // Check if the CSV file exists
            if (file_exists("./recipes/recipes.csv")) {
                $csvFile = fopen("./recipes/recipes.csv", "r");
                
                // Check if the CSV file was opened successfully
                if ($csvFile) {
                    $recipes = [];

                    while (($data = fgetcsv($csvFile))) {
                        $recipe = '<div class="recipe-card">';
                        $recipe .= '<h2>' . $data[1]. '</h2>';
                        $recipe .= '<p> Prep Time: ' . $data[4] . 'hrs ' . $data[5] . 'mins</p>';
                        $recipe .= '<p> Cook Time: ' . $data[6] . 'hrs ' . $data[7] . 'mins</p>';
                        $recipe .= '<p> Servings: ' . $data[3]. '</p>';
                        $recipe .= '<a href="details.php?id=' . $data[0] . '">View Recipe Details</a>';
                        $recipe .= '</div>';
                        
                        array_push($recipes, $recipe);
                    }

                    fclose($csvFile);
                    
                    // Check if the recipes array is empty
                    if (empty($recipes)) {
                        echo '<p>CSV is empty</p>';
                    } else {
                        // Render all recipes
                        echo '<h1>Recipes</h1>';
                        echo '<div class="recipe-list">';
                        foreach ($recipes as $recipe) {
                            echo $recipe;
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<p>Failed to open CSV file</p>';
                }
            } else {
                echo '<p>CSV file does not exist</p>';
            }

            include('./util/footer.php');
        ?>
    </body>
</html>