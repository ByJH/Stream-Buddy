<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "team_2";
$password = "hu900se2";
$dbname = "team_2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

$category = $_GET['category'] ?? 'All';
$platforms = isset($_GET['platforms']) ? explode(',', $_GET['platforms']) : [];

$sql = "SELECT mediaID, title, releasedate, description, genre, pictures, streaming_platform, category FROM Media WHERE 1=1";

if ($category !== 'All') {
    $sql .= " AND category = ?";
}
if (!empty($platforms)) {
    $placeholders = implode(',', array_fill(0, count($platforms), '?'));
    $sql .= " AND streaming_platform IN ($placeholders)";
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to prepare statement: " . $conn->error]);
    exit;
}

$bindTypes = '';
$params = [];

if ($category !== 'All') {
    $bindTypes .= 's';
    $params[] = $category;
}
if (!empty($platforms)) {
    foreach ($platforms as $platform) {
        $bindTypes .= 's';
        $params[] = $platform;
    }
}

if (!empty($params)) {
    $stmt->bind_param($bindTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$movies = [];
while ($row = $result->fetch_assoc()) {
    $row['pictures'] = $row['pictures'] ?: '/StreamBuddy/Frontend/images/default-pfp.png';
    $movies[] = $row;
}

if (empty($movies)) {
    echo json_encode(["success" => false, "message" => "No movies found", "data" => []]);
} else {
    echo json_encode(["success" => true, "data" => $movies]);
}

$stmt->close();
$conn->close();
?>
