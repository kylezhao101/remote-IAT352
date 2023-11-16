<?php 
    //redirect to http
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        // If HTTPS is on, redirect to HTTP
        $redirect_url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        header("Location: " . $redirect_url);
        exit();
    }
    session_start();

    $dbserver = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "classicmodels";

    $dbhost = new mysqli($dbserver,$dbusername,$dbpassword,$dbname);
    
    $sql = "SELECT `productName`, `productCode` FROM `products`";
    $result = $dbhost->query($sql);

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
        <?php include 'navbar.php'; ?>
        <h1>All Models</h1>

        <?php 
        
            //display models
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<p><a href='modeldetails.php?model_id=" . $row["productCode"] . "'>" . $row["productName"] . "</a></p>";
                }
            } else {
                echo "No models";
            }
        ?>
    </body>
</html>