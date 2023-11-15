<?php
// require_once('inialize.php');
session_start();
$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "classicmodels";

$dbhost = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);





// END TODO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: check for existing user account, if there is none, encrypt the password and save the entry
    // Make sure password matches
    // After the entry is inserted successfully, redirect to dashboard page
    if (
        isset($_POST['first_name']) &&
        isset($_POST['last_name']) &&
        isset($_POST['email']) &&
        isset($_POST['username']) &&
        isset($_POST['password']) &&
        isset($_POST['password_confirm'])
    ) {
        if ($_POST['password'] == $_POST['password_confirm']) {
            $sql = "select count(*) as count from admins where username=?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, "s", $_POST["username"]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                $res = mysqli_fetch_assoc($result);
                if ($res['count'] > 0) {
                    echo "This username is taken.";
                } else {
                    $hash_pass = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    $sql = "insert into admins (first_name, last_name, email, username, hashed_password) 
            values (?,?,?,?,?)";
                    $stmt = mysqli_prepare($db, $sql);
                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssss",
                        $_POST["first_name"],
                        $_POST['last_name'],
                        $_POST['email'],
                        $_POST['username'],
                        $hash_pass
                    );
                    $res = mysqli_stmt_execute($stmt);
                    if ($res) {
                        $_SESSION['username'] = $_POST['username'];
                        header("Location: index.php");
                    }
                }
            }
        }
        // END TODO
    }
}

?>

<?php $page_title = 'Register'; ?>
<?php include('header.php'); ?>

<div id="content">
    <h1>Register</h1>



    <form action="register.php" method="post">
        First Name:<br />
        <input type="text" name="first_name" value="" required /><br />
        Last Name:<br />
        <input type="text" name="last_name" value="" required /><br />
        Email:<br />
        <input type="text" name="email" value="" required /><br />
        Username:<br />
        <input type="text" name="username" value="" required /><br />
        Password:<br />
        <input type="password" name="password" value="" required /><br />
        Confirm Password:<br />
        <input type="password" name="password_confirm" value="" required /><br />
        <input type="submit" />
    </form>
</div>
