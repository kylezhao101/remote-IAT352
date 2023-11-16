<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $logged_in = true;
} else {
    $logged_in = false;
}

$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "classicmodels";

//if model_id in url is set
if (isset($_GET['model_id'])) {
    $model_id = $_GET['model_id'];

    $dbhost = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);

    //query for model 
    $sql = "SELECT * FROM `products` WHERE `productCode` = '$model_id'";
    $result = $dbhost->query($sql);

    //model details
    if ($result->num_rows > 0) {
        $modelDetails = $result->fetch_assoc();
    } else {
        $modelDetails = null;
    }

    $dbhost->close();
} else {
    //redirect
    header("Location: showmodels.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport">
    <title>Model Details</title>
    <style>
        table {
            width: 60%;
            border-collapse: collapse;
        }
        table,th,td {
            border: 1px solid black;
        }
        th,td {
            padding: 10px;
            text-align: left;
        }
    </style>
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

    <h1><?php echo $modelDetails['productName']; ?> Details</h1>
    <h2>Model details</h2>

    <?php
    //todo:
    //show add to watchlist link if logged in

    //details
    if ($modelDetails) {
        echo "<table>";
        echo "<tr><th>Product Code</th><td>{$modelDetails['productCode']}</td></tr>";
        echo "<tr><th>Product Name</th><td>{$modelDetails['productName']}</td></tr>";
        echo "<tr><th>Product Line</th><td>{$modelDetails['productLine']}</td></tr>";
        echo "<tr><th>Product Scale</th><td>{$modelDetails['productScale']}</td></tr>";
        echo "<tr><th>Product Vendor</th><td>{$modelDetails['productVendor']}</td></tr>";
        echo "<tr><th>Product Description</th><td>{$modelDetails['productDescription']}</td></tr>";
        echo "<tr><th>Quantity In Stock</th><td>{$modelDetails['quantityInStock']}</td></tr>";
        echo "<tr><th>Buy Price</th><td>{$modelDetails['buyPrice']}</td></tr>";
        echo "<tr><th>MSRP</th><td>{$modelDetails['MSRP']}</td></tr>";
        echo "</table>";
    } else {
        echo "Model not found";
    }
    ?>
</body>

</html>