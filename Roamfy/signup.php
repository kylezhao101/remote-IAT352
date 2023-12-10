<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';

function registerUser($db, $firstName, $lastName, $email, $username, $password, $passwordConfirm)
{
    // Check if passwords match
    if ($password !== $passwordConfirm) {
        return "Passwords do not match.";
    }

    // Check if email is already registered
    $sqlCheckEmail = "SELECT COUNT(*) AS count FROM member WHERE email=?";
    $stmtCheckEmail = $db->prepare($sqlCheckEmail);

    if (!$stmtCheckEmail) {
        return "Error preparing statement: " . $db->error;
    }

    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();
    $resultCheckEmail = $stmtCheckEmail->get_result();

    if ($resultCheckEmail) {
        $count = mysqli_fetch_assoc($resultCheckEmail)['count'];

        if ($count > 0) {
            return "This email is already registered.";
        }
    } else {
        return "Error checking email availability: " . $db->error;
    }

    // Insert user into the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sqlInsertUser = "INSERT INTO member(username, password, email) VALUES (?, ?, ?)";
    $stmtInsertUser = $db->prepare($sqlInsertUser);

    if (!$stmtInsertUser) {
        return "Error preparing statement: " . $db->error;
    }

    $stmtInsertUser->bind_param("sss", $username, $hashedPassword, $email);
    $resultInsertUser = $stmtInsertUser->execute();

    if ($resultInsertUser) {
        $_SESSION['username'] = $username;
        header("Location: login.php");
        exit();
    } else {
        return "Error registering user: " . $db->error;
    }
}

function handleRegistrationForm()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requiredFields = ['first_name', 'last_name', 'email', 'username', 'password', 'password_confirm'];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                return "All fields are required.";
            }
        }

        $db = connectToDatabase();
        $result = registerUser(
            $db,
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['username'],
            $_POST['password'],
            $_POST['password_confirm']
        );

        if ($result !== true) {
            echo $result;
        }
    }
}

handleRegistrationForm();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>
    <h3>Sign Up</h3>
    <form action="signup.php" method="post" class="auth-form">
        <!-- Add CSRF token for security -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(session_id()); ?>">

        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" placeholder="Enter your first name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" placeholder="Enter your last name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" placeholder="e.g. 123@email.com" required>

        <label for="username">Username:</label>
        <input type="text" name="username" placeholder="Choose a username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <label for="password_confirm">Confirm Password:</label>
        <input type="password" name="password_confirm" placeholder="Confirm your password" required>

        <input type="submit" value="Sign Up">
    </form>

</body>

</html>