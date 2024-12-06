<?php
// Include the database connection
include('config.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate that the username is not "Admin"
    if (strtolower($username) === "admin") {
        echo "The username 'Admin' is not allowed. Please choose a different username.";
    } else {
        // Simple validation: Ensure all fields are filled
        if (empty($email) || empty($username) || empty($password)) {
            echo "All fields are required.";
        } else {
            // Check if the username or email already exists in the database
            $sql = "SELECT * FROM Users WHERE username = '$username' OR email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "Username or email already exists. Please choose a different one.";
            } else {
                // Insert user data into the database
                $sql = "INSERT INTO Users (email, username, password) VALUES ('$email', '$username', '$password')";

                if ($conn->query($sql) === TRUE) {
                    // On success, redirect to the login page
                    echo "<script>alert('Signup successful! Please log in.'); window.location.href='../Frontend/pages/Login.html';</script>";
                } else {
                    echo "Error: " . $conn->error;
                }
            }
        }
    }
}

// Close the connection
$conn->close();
?>
