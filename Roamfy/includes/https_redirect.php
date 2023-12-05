<?php

function enforceHttps() {
    if ($_SERVER["HTTPS"] != "on") {
        $redirectUrl = "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        header("Location: " . $redirectUrl);
        exit();
    }
}
?>