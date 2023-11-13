<?php 
    $dbserver="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="classicmodels";

    $db = new mysqli($dbserver, $dbuser, $dbpass, $dbname);

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $orderNumber = $_POST["order_number"];
        $startDate =  $_POST["start_date"];
        $endDate = $_POST["end_date"];

        $selections = array();
        if (isset($_POST["display_order_number"]))$selections[] = "orders.orderNumber";
        if (isset($_POST["order_date"]))$selections[] = "orders.orderDate";
        if (isset($_POST["order_ship_date"]))$selections[] = "orders.shippedDate";
        if (isset($_POST["product_name"]))$selections[] = "products.productName";
        if (isset($_POST["product_description"]))$selections[] = "products.productDescription";
        if (isset($_POST["quantity_ordered"]))$selections[] = "orderdetails.quantityOrdered";
        if (isset($_POST["price_each"]))$selections[] = "orderdetails.priceEach";
    
        $selectQuery = implode(', ', $selections);

        $whereQuery = array();
        if(!empty($orderNumber)){
            $whereQuery[] = "orders.orderNumber=" . $orderNumber;
        }
        if(!empty($startDate) && !empty($endDate)){
            $whereQuery[] = "orderDate BETWEEN '$startDate' AND '$endDate'";
        }
        $whereQuery = implode(" AND ", $whereQuery);

        $query = "SELECT $selectQuery FROM orders 
        INNER JOIN orderdetails ON orders.orderNumber = orderdetails.orderNumber 
        INNER JOIN products ON orderdetails.productCode = products.productCode";
        if(!empty($whereQuery)){
            $query .= " WHERE $whereQuery";
        }

        $result = $db->query($query);
  
    }
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
        Order Number: <input type="text" name="order_number" value="<?php echo !empty($_POST['order_number']) ? $_POST['order_number'] : ''; ?>"> Or

        <br>Order Date (YYYY-MM-DD)
        from: <input type="text" name="start_date" value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : ''; ?>">
        to: <input type="text" name="end_date" value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ''; ?>">
        <br><br>

        <h2>Select Columns to Display</h2>
        <ul>
            <li><input type="checkbox" name="display_order_number" <?php echo isset($_POST['display_order_number']) ? 'checked' : ''; ?>> Order Number</li>
            <li><input type="checkbox" name="order_date" <?php echo isset($_POST['order_date']) ? 'checked' : ''; ?>> Order Date</li>
            <li><input type="checkbox" name="order_ship_date" <?php echo isset($_POST['order_ship_date']) ? 'checked' : ''; ?>> Order Ship Date</li>
            <li><input type="checkbox" name="product_name" <?php echo isset($_POST['product_name']) ? 'checked' : ''; ?>> Product Name</li>
            <li><input type="checkbox" name="product_description" <?php echo isset($_POST['product_description']) ? 'checked' : ''; ?>> Product Description</li>
            <li><input type="checkbox" name="quantity_ordered" <?php echo isset($_POST['quantity_ordered']) ? 'checked' : ''; ?>> Quantity Ordered</li>
            <li><input type="checkbox" name="price_each" <?php echo isset($_POST['price_each']) ? 'checked' : ''; ?>> Price Each</li>
        </ul>
        <br>

        <input type="submit" value="Search Orders">

        <h3>
            SQL Query
        </h3>
        <?php
            if (isset($query)) {
                echo "<p>$query</p>";
            }

            if (isset($result) && $result->num_rows > 0) {
                echo"<h3>Result</h3>";

                echo"<table border='1'>";
                //table headers
                $row = $result->fetch_assoc();
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<th>$key</th>";
                }
                echo "</tr>";
                //results
                do {
                    echo "<tr>";
                    foreach($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                } while ($row = $result->fetch_assoc());
                
                echo "</table>";   
            }
        ?>
    </form>
</body>
</html>