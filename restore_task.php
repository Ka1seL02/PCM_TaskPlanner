<?php
session_start();
include 'db/db_connection.php';

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Check if the task exists
    $check_sql = "SELECT * FROM tasks WHERE id = ? AND status = 'archived'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the task to restore it
        $restore_sql = "UPDATE tasks SET status = 'pending', archived_at = NULL WHERE id = ?";
        $stmt = $conn->prepare($restore_sql);
        $stmt->bind_param("i", $task_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Task successfully restored.";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['message'] = "Error restoring task.";
            $_SESSION['msg_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Task not found or already restored.";
        $_SESSION['msg_type'] = "warning";
    }
    
    $stmt->close();
    $conn->close();
    header("Location: admin_archived.php");
    exit();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['msg_type'] = "error";
    header("Location: admin_archived.php");
    exit();
}
?>
