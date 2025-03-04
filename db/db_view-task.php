<?php
include 'db_connection.php';

if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    $query = "SELECT t.*, u.fullname AS assigned_user 
              FROM tasks t
              LEFT JOIN users u ON t.assignedto = u.id
              WHERE t.id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    if ($task) {
        echo json_encode([
            "task_name" => $task['taskname'],
            "task_description" => $task['taskdescription'],
            "start_time" => $task['starttime'],
            "due_time" => $task['duetime'],
            "assigned_to" => $task['assigned_user'],
            "status" => $task['status'],
            "attachment" => $task['attachment'],
            "proof" => $task['proof']
        ]);
    } else {
        echo json_encode(["error" => "Task not found"]);
    }
}
?>