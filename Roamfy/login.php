<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
$db = connectToDatabase();

//login authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST["email"];
    $sql = "SELECT `email`, `password` FROM `member` WHERE email = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $password = mysqli_fetch_assoc($result)['password'];
        if (password_verify($_POST['password'], $password)) {
            $_SESSION['username'] = $username;

            //redirect to all models after successful login
            $redirect_url = isset($_SESSION['callback_url']) ? $_SESSION['callback_url'] : 'index.php';
            header("Location: $redirect_url");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport">
    <title>Log in</title>
</head>

<body>
<?php include 'layouts/navbar.php'; ?>
    <div id="content">
        <h1>Log in</h1>

        <form action="login.php" method="post">
            email:<br />
            <input type="text" name="email" value="" /><br />
            Password:<br />
            <input type="password" name="password" value="" /><br />
            <input type="submit" />
        </form>

        <p>Not registered yet? <a href="signup.php">Register here</a>.</p>

    </div>
</body>

</html>
