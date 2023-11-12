<?php 
    $dbserver="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="classicmodels";

    $db = new mysqli($dbserver, $dbuser, $dbpass, $dbname);


?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Query</title>
</head>
<body>
    <h1>Query</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h2>Select Order Parameters</h2>
        Order Number: <input type="text" name="order_number" value="<?php echo isset($_POST['order_number']) ? $_POST['order_number'] : ''; ?>"> Or

        <br>Order Date (YYYY-MM-DD)
        from: <input type="text" name="start_date" value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>">
        to: <input type="text" name="end_date" value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>">
        <br><br>

        <h2>Select Columns to Display</h2>
        <ul>
            <li><input type="checkbox" name="order_number" <?php echo isset($_POST['order_number']) ? 'checked' : ''; ?>> Order Number</li>
            <li><input type="checkbox" name="order_date" <?php echo isset($_POST['order_date']) ? 'checked' : ''; ?>> Order Date</li>
            <li><input type="checkbox" name="order_ship_date" <?php echo isset($_POST['order_ship_date']) ? 'checked' : ''; ?>> Order Ship Date</li>
            <li><input type="checkbox" name="product_name" <?php echo isset($_POST['product_name']) ? 'checked' : ''; ?>> Product Name</li>
            <li><input type="checkbox" name="product_description" <?php echo isset($_POST['product_description']) ? 'checked' : ''; ?>> Product Description</li>
            <li><input type="checkbox" name="quantity_ordered" <?php echo isset($_POST['quantity_ordered']) ? 'checked' : ''; ?>> Quantity Ordered</li>
            <li><input type="checkbox" name="price_each" <?php echo isset($_POST['price_each']) ? 'checked' : ''; ?>> Price Each</li>
        </ul>
        <br>

        <input type="submit" value="Search Orders">
    </form>
</body>
</html>