<?php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'path/to/error.log'); // Change this to the path where you want to log errors
error_reporting(E_ALL);

include 'db_connection.php'; // Ensure you include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task_id'])) {
        $taskId = $_POST['task_id'];

        // Delete task from the database
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
            exit();
        }
        $stmt->bind_param("i", $taskId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found or already deleted']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>