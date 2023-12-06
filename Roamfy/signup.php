<?php
session_start();

include 'db_connection.php';
include 'https_redirect.php';


function registerUser($db, $firstName, $lastName, $email, $username, $password, $passwordConfirm) {
    // Check if passwords match
    if ($password !== $passwordConfirm) {
        return "Passwords do not match.";
    }

    // Check if email is empty
    if (empty($email)) {
        return "Email cannot be empty.";
    }

    // Check if email is already registered
    $sql = "SELECT COUNT(*) AS count FROM member WHERE email=?";
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        return "Error preparing statement: " . $db->error;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $count = mysqli_fetch_assoc($result)['count'];

        if ($count > 0) {
            return "This email is already registered.";
        } else {
            // Insert user into the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO member (username, password, email) 
                    VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql);

            if (!$stmt) {
                return "Error preparing statement: " . $db->error;
            }

            $stmt->bind_param("sss", $username, $hashedPassword, $email);
            $result = $stmt->execute();

            if ($result) {
                $_SESSION['username'] = $username;
                header("Location: login.php");
                exit();
            } else {
                return "Error registering user: " . $db->error;
            }
        }
    }

    return "Error checking email availability: " . $db->error;
}

function handleRegistrationForm() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requiredFields = ['first_name', 'last_name', 'email', 'username', 'password', 'password_confirm'];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                echo "All fields are required.";
                return;
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
    <title>SignUp</title>
</head>
 <?php include 'layouts/navbar.php'; ?>
<body>

    <h1>SignUp</h1>

    <form action="signup.php" method="post">
        <!-- Add CSRF token for security -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(session_id()); ?>">

        <label for="first_name">First Name:</label><br>
        <input type="text" name="first_name" required><br>

        <label for="last_name">Last Name:</label><br>
        <input type="text" name="last_name" required><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" required><br>

        <label for="username">Username:</label><br>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" required><br>

        <label for="password_confirm">Confirm Password:</label><br>
        <input type="password" name="password_confirm" required><br>

        <button type="submit">SignUp</button>
    </form>
</body>

</html>
