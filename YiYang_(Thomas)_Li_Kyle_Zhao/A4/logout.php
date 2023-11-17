<?php
session_start();
// Unset all of the session
$_SESSION = array();
session_destroy();
// Redirect to the login page after logout
header("Location: showmodels.php");
exit();
