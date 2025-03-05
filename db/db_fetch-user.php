<?php
header('Content-Type: application/json');

require_once 'db_connection.php'; // Ensure you include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // Fetch user details from the database
        $stmt = $conn->prepare("SELECT id, pfp, fullname, username, email, department, school FROM users WHERE id = ?");
        if ($stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
            exit();
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>