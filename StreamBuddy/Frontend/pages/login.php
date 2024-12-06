<?php
// Include the database connection
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

// Start session for user state
session_start();

// Define error message variable
$errorMessage = "";

// Check if the form is submitted via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginInput = $_POST['login'];  // Username or Email
    $password = $_POST['password']; // Password
    
    if ($loginInput === "Admin" && $password === "123") {
        // Admin login credentials matched
        $_SESSION['username'] = "Admin";
        $_SESSION['admin'] = true; // Set admin flag to true
        echo json_encode(["status" => "success", "message" => "Admin login successful"]);
        exit();
    }
    // Search for user by username or email
    $sql = "SELECT userID, email, username, password FROM Users WHERE email = '$loginInput' OR username = '$loginInput'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();

        // Check if password matches
        if ($row['password'] === $password) {
            // Store session data
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['username'] = $row['username'];

            // Respond with success status and user data
            echo json_encode(["status" => "success", "message" => "Login successful"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password. Please try again."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid username or email. Please try again."]);
    }

    // Close the database connection
    $conn->close();
}
?>
