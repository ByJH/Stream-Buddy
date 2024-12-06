<link rel="stylesheet" href="../styles/Profile.css">
<?php
// Correct path to config.php file located in the backend directory
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

// Check if the user is logged in, if not, redirect to login page
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Get the current user's data
$userID = $_SESSION['userID'];
$sql = "SELECT * FROM Users WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userID);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? $userResult['username'];
    $email = $_POST['email'] ?? $userResult['email'];
    
    // If a new password is provided, hash and update it
    $password = $_POST['password'];
    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // If no password is provided, keep the existing one
        $passwordHash = $userResult['password'];
    }
    
    // Update the user's profile
    $updateSql = "UPDATE Users SET username = ?, email = ?, password = ? WHERE userID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('sssi', $username, $email, $passwordHash, $userID);
    
    if ($updateStmt->execute()) {
        echo "Profile updated successfully!";
        // Refresh user data
        $userResult['username'] = $username;
        $userResult['email'] = $email;
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($userResult['username']); ?>!</h1>
    
    <!-- Profile Edit Form -->
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userResult['username']); ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userResult['email']); ?>" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password"><br><br>

        <button type="submit">Save Changes</button>
    </form>

    <!-- Home Button -->
    <br>
    <a href="Home.html"><button>Home</button></a>

    <br>
    <a href="Logout.html">Logout</a>
</body>
</html>
