<?php
session_start();
$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "classicmodels";

$db = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['first_name']) &&
        isset($_POST['last_name']) &&
        isset($_POST['email']) &&
        isset($_POST['username']) &&
        isset($_POST['password']) &&
        isset($_POST['password_confirm'])
    ) {
        // echo "Password: " . $_POST['password'] . "<br>";
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
                    }
                }
            }
        } else {
            echo "Passwords do not match.";
        }
    }
}
?>
