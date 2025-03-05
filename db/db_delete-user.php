<?php
header('Content-Type: application/json');

require_once 'db_connection.php'; // Ensure you include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // Start a transaction
        $conn->begin_transaction();

        try {
            // Delete tasks assigned to the user
            $taskStmt = $conn->prepare("DELETE FROM tasks WHERE assignedto = ?");
            if ($taskStmt === false) {
                throw new Exception('Failed to prepare task deletion statement');
            }
            $taskStmt->bind_param("i", $userId);
            $taskStmt->execute();
            $taskStmt->close();

            // Delete user from the database
            $userStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            if ($userStmt === false) {
                throw new Exception('Failed to prepare user deletion statement');
            }
            $userStmt->bind_param("i", $userId);
            $userStmt->execute();

            if ($userStmt->affected_rows > 0) {
                // Commit the transaction
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'User and their tasks deleted successfully']);
            } else {
                throw new Exception('User not found or already deleted');
            }
            $userStmt->close();
        } catch (Exception $e) {
            // Rollback the transaction
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>