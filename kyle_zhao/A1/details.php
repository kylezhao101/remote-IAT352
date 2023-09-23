<!DOCTYPE html>
<html land='en'>
    <head>
        <meta charset="UTF-8">
        <title>Recipe Details</title>
        <!-- CSS -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <?php
            function decodeCommas($text){
                return str_replace('#', ',', $text);
            };

            include('./util/nav.php');

            if (isset($_GET['id'])) {
                $recipeId = $_GET['id'];

                $csvFile = fopen("./recipes/recipes.csv", "r");

                if ($csvFile) {
                    while (($data = fgetcsv($csvFile))){
                        if ($data[0] == $recipeId){
                            $title = $data[1];
                            $description = str_replace('#', ',', $data[2]);
                            $serving = $data[3];
                            $prep_hr = $data[4];
                            $prep_min = $data[5];
                            $cook_hr = $data[6];
                            $cook_min = $data[7];

                            $encodedIngredients = $data[8];
                            $ingredients = explode('+++', $encodedIngredients);
                            $instructions = str_replace('@@@', "\n\n", $data[9]);
                            $instructions = decodeCommas($instructions);
                            $tags = str_replace('#', ',', $data[10]);
                            
                            // Display the recipe details
                            echo '<div class="recipe-detail-card">';
                            echo "<h1>$title</h1>";
                            echo '<div class="recipe-details-info">';
                            echo "<p><strong>Prep Time:</strong> $prep_hr hr $prep_min min</p>";
                            echo "<p><strong>Cook Time:</strong> $cook_hr hr $cook_min min</p>";
                            echo "<p><strong>Serving(s):</strong> $serving</p>";
                            echo '</div>';
                            echo "<p>$description</p>";
                            echo "<h2><strong>Ingredients:</strong></h2>";
                            echo "<ul>";
                            foreach ($ingredients as $ingredient) {
                                echo "<li>$ingredient</li>";
                            }
                            echo "</ul>";
                            echo "<h2><strong>Instructions:</strong></h2>";
                            echo "<ol>";
                            $instructionLines = explode("\n\n", $instructions);
                            foreach ($instructionLines as $line) {
                                echo "<li>$line</li>";
                            }
                            echo "</ol>";
                            echo "<p><strong>Tags:</strong> $tags</p>";
                            echo "</div>";

                            break;

                        }
                    }
                } else {
                    echo "Failed to open the CSV file for Reading.";
                }

            } else {
                echo "No Recipe ID";
            }
            include('./util/footer.php');
        ?>
    </body>
</html