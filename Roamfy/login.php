<?php

// Includes
include 'includes/db_connection.php';
include 'includes/https_redirect.php';

// Function for login authentication
function authenticateLogin($db) {
    // Check if the form has been submitted using POST method
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the username and password from the form
        $username = $_POST["email"];

        // Prepare a SQL query to retrieve the encrypted password for the provided username
        $sql = "SELECT `email`, `encrypted_password` FROM `users` WHERE email = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if a user with the provided username exists
        if (mysqli_num_rows($result) > 0) {
            // Retrieve the stored encrypted password from the database result
            $password = mysqli_fetch_assoc($result)['encrypted_password'];

            // Verify the provided password against the stored encrypted password
            if (password_verify($_POST['password'], $password)) {
                // If the passwords match, set the username in the session
                $_SESSION['username'] = $username;

                // Redirect to the specified callback URL after successful login
                $redirect_url = isset($_SESSION['callback_url']) ? $_SESSION['callback_url'] : 'showmodels.php';
                header("Location: $redirect_url");
            }
        }
    }
}

session_start();
enforceHttps();
$db = connectToDatabase();
include 'layouts/navbar.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport">
    <title>Log in</title>
</head>

<body>
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
