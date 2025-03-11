<?php
// db/db_add-comment.php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Check if required data is provided
if (!isset($_POST['task_id']) || !isset($_POST['comment']) || empty($_POST['task_id']) || empty($_POST['comment'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

$task_id = $_POST['task_id'];
$comment = $_POST['comment'];
$admin_id = $_SESSION['id'];

// Update the comments field in the tasks table
$query = "UPDATE tasks SET comments = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $comment, $task_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$stmt->close();
$conn->close();
?>