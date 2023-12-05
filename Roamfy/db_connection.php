<?php
$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "classicmodels";

$db = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>