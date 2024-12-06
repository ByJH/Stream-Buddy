<?php
$servername = "localhost";
$username = "team_2";
$password = "hu900se2";
$dbname = "team_2"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all reviews from the table
    $stmt = $conn->prepare("SELECT movie_name, review_body, rating, reviewer_name, review_date FROM reviews");
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    if (count($reviews) > 0) {
        echo json_encode(["success" => true, "reviews" => $reviews]);
    } else {
        echo json_encode(["success" => false, "message" => "No reviews found"]);
    }

    $stmt->close();
}

$conn->close();
?>
