<?php 

    //list of model names
    //model opens to modeldetails.php
    session_start();
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $logged_in = true;
    } else {
        $logged_in = false;
    }

    $dbserver = "localhost";
    $dbusername = "";
    $dbpassword = "";
    $dbname = "classicmodels";

    $dbhost = new mysqli($dbserver,$dbusername,$dbpassword,$dbname);

    $sql = "SELECT products.productCode, products.productName FROM products";
    $result = $db->query($sql);

    $dbhost->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport">
        <title>All Models</title>
    </head>
    <body>
        <?php
        //navigation links
        //TODO
        //all models, watchlist, login/logout

        if ($logged_in) {
            echo "<p><a href='watchlist.php'>Watchlist</a></p>";
        } else {
            echo "<p><a href='login.php'>Login</a></p>";
        }


        ?>
        <h1>All Models</h1>

        <?php 
        if ($logged_in) {
            //display models
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<p><a href='modeldetails.php?model_id=" . $row["productCode"] . "'>" . $row["productName"] . "</a></p>";

                    //todo:
                    //show add to watchlist link if logged in

                    echo "<br>";
                }
            } else {
                echo "No models";
            }
        }
        ?>
    </body>
</html>