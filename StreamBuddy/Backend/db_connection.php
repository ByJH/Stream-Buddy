<?php
$servername = "localhost";
$username = "team_2";//change
$password = "hu900se2";//chage
$dbname = "team_2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>