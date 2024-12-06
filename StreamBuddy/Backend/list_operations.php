<?php
session_start();
include 'db_connection.php';

function getUserLists($userID) {
    global $conn;
    $lists = array();
    
    $query = "
        SELECT l.*, COUNT(li.mediaID) as item_count 
        FROM Lists l 
        LEFT JOIN ListItems li ON l.listID = li.listID 
        WHERE l.userID = ? 
        GROUP BY l.listID
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $lists[] = $row;
    }
    
    return $lists;
}

function getMediaByGenre($genre = null, $page = 1, $limit = 20) {
    global $conn;
    $offset = ($page - 1) * $limit;
    
    $query = "SELECT * FROM Media";
    if ($genre) {
        $query .= " WHERE genre = ?";
    }
    $query .= " LIMIT ? OFFSET ?";
    
    if ($genre) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $genre, $limit, $offset);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $media = array();
    
    while ($row = $result->fetch_assoc()) {
        $media[] = $row;
    }
    
    return $media;
}

function getMediaCount($genre = null) {
    global $conn;
    
    $query = "SELECT COUNT(*) as total FROM Media";
    if ($genre) {
        $query .= " WHERE genre = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $genre);
    } else {
        $stmt = $conn->prepare($query);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userID = $_SESSION['userID'] ?? null;
    
    if (!$userID) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }
    
    switch ($action) {
        case 'create_list':
            $listName = $_POST['listName'];
            $mediaItems = json_decode($_POST['mediaItems']);
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Create new list
                $stmt = $conn->prepare("INSERT INTO Lists (name, userID) VALUES (?, ?)");
                $stmt->bind_param("si", $listName, $userID);
                $stmt->execute();
                $listID = $conn->insert_id;
                
                // Add media items to list
                if (!empty($mediaItems)) {
                    $stmt = $conn->prepare("INSERT INTO ListItems (listID, mediaID) VALUES (?, ?)");
                    foreach ($mediaItems as $mediaID) {
                        $stmt->bind_param("ii", $listID, $mediaID);
                        $stmt->execute();
                    }
                }
                
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'List created successfully']);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Error creating list: ' . $e->getMessage()]);
            }
            break;
            
        case 'delete_list':
            $listID = $_POST['listID'];
            
            $conn->begin_transaction();
            
            try {
                // Delete list items first
                $stmt = $conn->prepare("DELETE FROM ListItems WHERE listID = ?");
                $stmt->bind_param("i", $listID);
                $stmt->execute();
                
                // Delete the list
                $stmt = $conn->prepare("DELETE FROM Lists WHERE listID = ? AND userID = ?");
                $stmt->bind_param("ii", $listID, $userID);
                $stmt->execute();
                
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'List deleted successfully']);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Error deleting list: ' . $e->getMessage()]);
            }
            break;
            
        case 'get_media':
            $genre = $_POST['genre'] ?? null;
            $media = getMediaByGenre($genre);
            echo json_encode(['success' => true, 'data' => $media]);
            break;
            
        case 'get_lists':
            $lists = getUserLists($userID);
            echo json_encode(['success' => true, 'data' => $lists]);
            break;
            
        case 'edit_list':
            $listID = $_POST['listID'];
            $listName = $_POST['listName'];

            $stmt = $conn->prepare("UPDATE Lists SET name = ? WHERE listID = ?");
            $stmt->bind_param("si", $listName, $listID);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update list']);
            }

            $stmt->close();
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userID = $_SESSION['userID'] ?? null;
    if ($userID) {
        $lists = getUserLists($userID);
        echo json_encode(['success' => true, 'data' => $lists]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
    }
}
?> 