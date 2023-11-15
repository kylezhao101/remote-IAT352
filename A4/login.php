<?php
session_start();
$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "classicmodels";

$db = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST["username"];
    $sql = "select email, encrypted_password from users where email = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    echo "" . mysqli_num_rows($result);
    if (mysqli_num_rows($result) > 0) {
        $password = mysqli_fetch_assoc($result)['hashed_password'];
        if (password_verify($_POST['password'], $password)) {
            $_SESSION['username'] = $username;
          
        }
    }

    // END TODO
}

?>

<?php $page_title = 'Log in'; ?>


<div id="content">
    <h1>Log in</h1>



    <form action="login.php" method="post">
        Username:<br />
        <input type="text" name="username" value="" /><br />
        Password:<br />
        <input type="password" name="password" value="" /><br />
        <input type="submit" />
    </form>

</div>
