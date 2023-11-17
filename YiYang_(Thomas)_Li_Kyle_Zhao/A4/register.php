<?php
session_start();
$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "classicmodels";

$db = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);

if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['first_name']) &&
        isset($_POST['last_name']) &&
        isset($_POST['email']) &&
        isset($_POST['username']) &&
        isset($_POST['password']) &&
        isset($_POST['password_confirm'])
    ) {
        // echo "Password: " . $_POST['passwo2rd'] . "<br>";
        // echo "Password Confirm: " . $_POST['password_confirm'] . "<br>";
        if ($_POST['password'] === $_POST['password_confirm']) {
            $sql = "SELECT COUNT(*) AS count FROM users WHERE email=?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "s", $_POST["email"]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                $res = mysqli_fetch_assoc($result);

                if ($res['count'] > 0) {
                    echo "This email is already registered.";
                } else {
                    $hash_pass = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (first_name, last_name, email, encrypted_password) 
                            VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($db, $sql);
                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssss",
                        $_POST["first_name"],
                        $_POST['last_name'],
                        $_POST['email'],
                        $hash_pass
                    );
                    $res = mysqli_stmt_execute($stmt);
                    if ($res) {
                        $_SESSION['username'] = $_POST['username'];
                        echo "Successfully registered!";

                        //redirect to login on success
                        header("Location: login.php");
                    }
                }
            }
        } else {
            echo "Passwords do not match.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport">
    <title>Register</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <h1>Register</h1>

    <form action="register.php" method="post">
        <label for="first_name">First Name:</label>
        <br>
        <input type="text" name="first_name" required>
        <br>

        <label for="last_name">Last Name:</label>
        <br>
        <input type="text" name="last_name" required>
        <br>

        <label for="email">Email:</label>
        <br>
        <input type="email" name="email" required>
        <br>

        <label for="username">Username:</label>
        <br>
        <input type="text" name="username" required>
        <br>

        <label for="password">Password:</label>
        <br>
        <input type="password" name="password" required>
        <br>

        <label for="password_confirm">Confirm Password:</label>
        <br>
        <input type="password" name="password_confirm" required>
        <br>

        <button type="submit">Register</button>
    </form>

</body>

</html>