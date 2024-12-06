<?php
$host = 'localhost';       // Your MySQL host (localhost for local environment)
$username = 'team_2';        // Default MySQL username for XAMPP
$password = 'hu900se2';            // Default MySQL password for XAMPP (empty by default)
$dbname = 'team_2';     // The name of your database

// Create a new connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
