<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost";
$username = "team_2";
$password = "hu900se2";
$dbname = "team_2"; // your database name

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $movieName = $_POST['movie_name'] ?? null;
    $reviewBody = $_POST['review_body'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $reviewerName = $_POST['reviewer_name'] ?? "Anonymous";

    
    if (!$movieName || !$reviewBody || !$rating) {
        echo json_encode(["success" => false, "message" => "Missing required fields."]);
        exit;
    }

    
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        echo json_encode(["success" => false, "message" => "Invalid rating. Rating must be between 1 and 5."]);
        exit;
    }


    $stmt = $conn->prepare("INSERT INTO reviews (movie_name, review_body, rating, reviewer_name) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Failed to prepare statement: " . $conn->error]);
        exit;
    }


    $stmt->bind_param("ssis", $movieName, $reviewBody, $rating, $reviewerName);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Review added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding review: " . $stmt->error]);
    }

    $stmt->close();
} else {
    
    echo json_encode(["success" => false, "message" => "Invalid request method. Use POST."]);
}

$conn->close();
?>
