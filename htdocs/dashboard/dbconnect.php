<?php

$db_host = "localhost";
$db_user = "username";
$db_pass = "default";
$db_name = "db";


$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
