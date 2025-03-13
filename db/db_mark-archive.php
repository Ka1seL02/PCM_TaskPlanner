<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'];

    // Update the task status to 'completed'
    $sql = "UPDATE tasks SET status = 'archived' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $taskId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>