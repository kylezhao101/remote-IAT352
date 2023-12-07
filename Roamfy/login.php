<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
$db = connectToDatabase();

// Login authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST["email"];

    // Prepare and execute the query to retrieve user data
    $sql = "SELECT `member_id`, `email`, `password` FROM `member` WHERE email = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if user exists
    if (mysqli_num_rows($result) > 0) {
        // Fetch user data
        $user = mysqli_fetch_assoc($result);
        $password = $user['password'];

        // Verify the password
        if (password_verify($_POST['password'], $password)) {
            // Set session variables on successful login
            $_SESSION['username'] = $username;
            $_SESSION['member_id'] = $user['member_id'];
            
            // Redirect to the specified URL or default to index.php
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>
    <div id="content">
        <h1>Log in</h1>

        <!-- Login form -->
        <form action="login.php" method="post">
            email:<br />
            <input type="text" name="email" value="" /><br />
            Password:<br />
            <input type="password" name="password" value="" /><br />
            <input type="submit" />
        </form>

        <!-- Registration link -->
        <p>Not registered yet? <a href="signup.php">Register here</a>.</p>
    </div>
</body>

</html>
