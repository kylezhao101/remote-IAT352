<?php
session_start();
include 'includes/db_connection.php';
include 'includes/https_redirect.php';

function changePassword($db, $userId, $currentPassword, $newPassword, $confirmPassword)
{
    // Check if new passwords match
    if ($newPassword !== $confirmPassword) {
        return "New passwords do not match.";
    }

    // Retrieve the current password from the database
    $sqlGetPassword = "SELECT password FROM member WHERE member_id=?";
    $stmtGetPassword = $db->prepare($sqlGetPassword);

    if (!$stmtGetPassword) {
        return "Error preparing statement: " . $db->error;
    }

    $stmtGetPassword->bind_param("i", $userId);
    $stmtGetPassword->execute();
    $resultGetPassword = $stmtGetPassword->get_result();

    if (!$resultGetPassword) {
        return "Error retrieving current password: " . $db->error;
    }

    $row = $resultGetPassword->fetch_assoc();
    $currentHashedPassword = $row['password'];

    // Verify the current password
    if (!password_verify($currentPassword, $currentHashedPassword)) {
        return "Current password is incorrect.";
    }

    // Update the password in the database
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sqlUpdatePassword = "UPDATE member SET password=? WHERE member_id=?";
    $stmtUpdatePassword = $db->prepare($sqlUpdatePassword);

    if (!$stmtUpdatePassword) {
        return "Error preparing statement: " . $db->error;
    }

    $stmtUpdatePassword->bind_param("si", $newHashedPassword, $userId);
    $resultUpdatePassword = $stmtUpdatePassword->execute();

    if ($resultUpdatePassword) {
        return true; // Password successfully updated
    } else {
        return "Error updating password: " . $db->error;
    }
}

function handleChangePasswordForm()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $requiredFields = ['current_password', 'new_password', 'confirm_password'];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                echo "All fields are required.";
                return;
            }
        }

        // Ensure the user is logged in
        if (!isset($_SESSION['member_id'])) {
            echo "User not logged in.";
            return;
        }

        $db = connectToDatabase();
        $result = changePassword(
            $db,
            $_SESSION['member_id'],
            $_POST['current_password'],
            $_POST['new_password'],
            $_POST['confirm_password']
        );

        if ($result === true) {
            echo "Password successfully changed.";
        } else {
            echo $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>
    <h3>Change Password</h3>
    <form action="settings.php" method="post" class="auth-form">
        <!-- Add CSRF token for security -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(session_id()); ?>">

        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" placeholder="Enter your current password" required>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" placeholder="Enter your new password" required>

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" placeholder="Confirm your new password" required>

        <input type="submit" value="Change Password">
    </form>
</body>

</html>

<?php handleChangePasswordForm(); ?>
