<!DOCTYPE html>
<html land='en'>
    <head>
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
                        foreach ($ingredients as &$ingredient) {
                            $ingredient = str_replace('#', ',', $ingredient);
                        }

                        $instructions = str_replace('@@@', "\n", $data[9]);
                        $instructions = decodeCommas($instructions);
                        $tags = str_replace('#', ',', $data[10]);
                        
                        // Display the recipe details
                        echo "<h1>$title</h1>";
                        echo "<p><strong>Description:</strong> $description</p>";
                        echo "<p><strong>Serving:</strong> $serving</p>";
                        echo "<p><strong>Prep Time:</strong> $prep_hr hr $prep_min min</p>";
                        echo "<p><strong>Cook Time:</strong> $cook_hr hr $cook_min min</p>";
                        echo "<p><strong>Ingredients:</strong></p>";
                        echo "<ul>";
                        foreach ($ingredients as $ingredient) {
                            echo "<li>$ingredient</li>";
                        }
                        echo "</ul>";
                        echo "<p><strong>Instructions:</strong></p>";
                        echo "<pre>$instructions</pre>";
                        echo "<p><strong>Tags:</strong> $tags</p>";

                        break;

                    }
                }
            } else {
                echo "Failed to open the CSV file for Reading.";
            }

        } else {
            echo "No Recipe ID";
        }

    ?>
</body>
</html