<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add a New Recipe</title>
    <!-- CSS -->
</head>

<body>
<?php

    include('./util/nav.php');

    // decoding helper function

    function decodeCommas($text){
        return str_replace('#',',', $text);
    };

    if (file_exists(("recipes.csv"))){
        $csvFile = fopen("recipes.csv", "r");
        
        if ($csvFile){
            $recipes = [];
    
            while (($data = fgetcsv($csvFile))){
                $description = decodeCommas($data[2]);
    
                $recipeLink = '<a href="recipe-details.php?id=' . $data[0] . '">View Recipe Details</a>';
    
                $recipe = '<li>';
                $recipe .= '<h2>' . $data[1]. '</h2>';
                $recipe .= '<p> Servings: ' . $data[3]. '</p>';
                $recipe .= '<p> Prep Time: ' . $data[4] . 'hrs ' . $data[5] . 'mins</p>';
                $recipe .= '<p> Cook Time: ' . $data[6] . 'hrs ' . $data[7] . 'mins</p>';
                $recipe .= '</li>';
                
                array_push($recipes, $recipe);
            }
    
            fclose($csvFile);
            // render all recipes
            if ($recipes){
                echo '<ul>';
                $recipeCount = count($recipes);
                for($i = 0; $i < $recipeCount; $i++){
                    echo $recipes[$i];
                }
                echo '</u>l';
            } 
        }
    } 
     else {
        echo 'failed to read csv file';
    }
?>
</body>
</html>