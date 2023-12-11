<?php

function enforceHttps() {
    if ($_SERVER["HTTPS"] != "on") {
        $redirectUrl = "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        header("Location: " . $redirectUrl);
        exit();
    }
}
function enforceHTTP() {
    // Check if 'HTTPS' index exists in $_SERVER array
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $redirect_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $redirect_url");
        exit();
    }
}
?>