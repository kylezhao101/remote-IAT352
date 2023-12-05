<?php
// Read the API key from config.ini
$config = parse_ini_file('config.ini');
$apiKey = $config['GEOAPIFY_API_KEY'];

// Return the API key as the response
echo json_encode(['apiKey' => $apiKey]);
?>