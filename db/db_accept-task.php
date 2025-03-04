<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["taskId"])) {
    $taskId = $_POST["taskId"];

    // Prepare SQL query to update task status
    $query = "UPDATE tasks SET status = 'in-progress' WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL preparation failed"]);
        exit;
    }

    $stmt->bind_param("i", $taskId);
    $executeResult = $stmt->execute();

    if ($executeResult) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "SQL execution failed: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
