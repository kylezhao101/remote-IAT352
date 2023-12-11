<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';
enforceHttps();
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
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>
    <div class="auth-content">
        <h3>Log in</h3>

        <!-- Login form -->
        <form action="login.php" method="post" class="auth-form">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" placeholder="e.g. 123@email.com" />

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" />

            <input type="submit" value="Log in" />
        </form>

        <!-- Registration link -->
        <p>Not registered yet? <a href="signup.php">Register here</a>.</p>
    </div>
</body>

</html>