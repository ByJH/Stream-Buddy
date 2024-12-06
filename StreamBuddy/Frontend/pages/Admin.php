<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles/Admin.css">
</head>
<body>

<?php
// Database connection
$servername = "localhost";
$username = "team_2";
$password = "hu900se2";
$dbname = "team_2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Check if the user is the admin
if ($_SESSION['username'] !== 'Admin') {
    // Redirect non-admin users to the home page
    header("Location: Home.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_review'])) {
    $user_id = $_POST['user_id']; // Get the user ID (it should be passed from the form)
    $media_id = $_POST['media_id']; // Get the media ID (it should be passed from the form)
    $review_text = $_POST['review_text']; // Get the review text

    // Ensure the user ID exists before inserting the review
    $result = $conn->query("SELECT userID FROM Users WHERE userID = '$user_id'");
    if ($result->num_rows > 0) {
        // User exists, now insert the review into the 'Reviews' table
        $sql = "INSERT INTO Reviews (userID, mediaID, text) VALUES ('$user_id', '$media_id', '$review_text')";
        if ($conn->query($sql) === TRUE) {
            echo "Review added successfully.";
        } else {
            echo "Error adding review: " . $conn->error;
        }
    } else {
        echo "Invalid User ID. The user does not exist.";
    }
}





// Add user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $sql = "INSERT INTO Users (username, email, password) VALUES ('$new_username', '$new_email', '$new_password')";
    $conn->query($sql);
}



?>
<div class="accent-background">
    <div class="content">
        <h1>Admin Dashboard</h1>
        <div class="button-container">
                <a href="Home.html" class="home-button">Home</a>
            </div>
        
        <!-- Search Users -->
        <div class="form-container">
            <h3>Search Users</h3>
            <form method="get">
                <input type="text" name="search_users" placeholder="Search by username, email, or ID">
                <input type="submit" value="Search">
            </form>
            <?php
            // Handle user search
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_users'])) {
    $search_query = $conn->real_escape_string($_GET['search_users']);
    $user_search_result = $conn->query("SELECT userID, username, email FROM Users WHERE username LIKE '%$search_query%' OR email LIKE '%$search_query%' OR userID = '$search_query'");
    if ($user_search_result->num_rows > 0) {
        echo "<h3>Search Results for Users</h3>";
        echo "<table><thead><tr><th>User ID</th><th>Username</th><th>Email</th></tr></thead><tbody>";
        while ($user = $user_search_result->fetch_assoc()) {
            echo "<tr><td>{$user['userID']}</td><td>{$user['username']}</td><td>{$user['email']}</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No users found matching '$search_query'.</p>";
    }
}
?>
        </div>

        <!-- Search Reviews -->
        <div class="form-container">
            <h3>Search Reviews</h3>
            <form method="get">
                <input type="text" name="search_reviews" placeholder="Search by media title or review ID">
                <input type="submit" value="Search">
            </form>
            <?php
            // Handle review search
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_reviews'])) {
    $search_query = $conn->real_escape_string($_GET['search_reviews']);
    $review_search_result = $conn->query("SELECT r.reviewID, m.title AS mediaTitle, r.text FROM Reviews r JOIN Media m ON r.mediaID = m.mediaID WHERE m.title LIKE '%$search_query%' OR r.reviewID = '$search_query'");
    if ($review_search_result->num_rows > 0) {
        echo "<h3>Search Results for Reviews</h3>";
        echo "<table><thead><tr><th>Review ID</th><th>Media Title</th><th>Review Text</th></tr></thead><tbody>";
        while ($review = $review_search_result->fetch_assoc()) {
            echo "<tr><td>{$review['reviewID']}</td><td>{$review['mediaTitle']}</td><td>{$review['text']}</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No reviews found matching '$search_query'.</p>";
    }
}
?>
        </div>

        <!-- Add New User -->
        <div class="form-container">
            <h3>Add New User</h3>
            <form method="post">
                <input type="hidden" name="add_user">
                <input type="text" name="username" placeholder="Enter new username" required>
                <input type="email" name="email" placeholder="Enter email" required>
                <input type="password" name="password" placeholder="Enter password" required>
                <input type="submit" value="Add User">
            </form>
        </div>


        <!-- Display Users -->
        <div class="form-container">
            <h3>Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = $conn->query("SELECT userID, username, email FROM Users");
                    while ($user = $users->fetch_assoc()) {
                        echo "<tr>
                            <td>{$user['userID']}</td>
                            <td>{$user['username']}</td>
                            <td>{$user['email']}</td>
                            <td>
                                <form method='post' style='display:inline'>
                                    <input type='hidden' name='delete_user' value='{$user['userID']}'>
                                    <input type='submit' value='Delete' class='btn-delete'>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Display Reviews -->
        <div class="form-container">
            <h3>Reviews</h3>
            <table>
                <thead>
                    <tr>
                        <th>Review ID</th>
                        <th>Media Title</th>
                        <th>Review Text</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $reviews = $conn->query("SELECT r.reviewID, m.title AS mediaTitle, r.text FROM Reviews r JOIN Media m ON r.mediaID = m.mediaID");
                    while ($review = $reviews->fetch_assoc()) {
                        echo "<tr>
                            <td>{$review['reviewID']}</td>
                            <td>{$review['mediaTitle']}</td>
                            <td>{$review['text']}</td>
                            <td>
                                <form method='post' style='display:inline'>
                                    <input type='hidden' name='delete_review' value='{$review['reviewID']}'>
                                    <input type='submit' value='Delete' class='btn-delete'>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 Stream Buddy. Admin Panel.</p>
</footer>

<?php
// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['delete_user'];

    // Step 1: Delete all reviews associated with the user
    $conn->query("DELETE FROM Reviews WHERE userID = $user_id");

    // Step 2: Delete dependent records from the 'listitems' table
    $conn->query("DELETE FROM listitems WHERE listID IN (SELECT listID FROM lists WHERE userID = $user_id)");

    // Step 3: Delete dependent records from the 'lists' table
    $conn->query("DELETE FROM lists WHERE userID = $user_id");

    // Step 4: Delete the user from the 'Users' table
    $conn->query("DELETE FROM Users WHERE userID = $user_id");

    // Optional: Check for errors
    if ($conn->error) {
        echo "Error deleting user: " . $conn->error;
    } else {
        echo "User and associated reviews deleted successfully.";
    }
}

// Handle review deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_review'])) {
    $review_id = $_POST['delete_review'];
    $conn->query("DELETE FROM Reviews WHERE reviewID = $review_id");
}

$conn->close();
?>

</body>
</html>
