<?php
// filepath: /c:/xampp/htdocs/taskplanner/db/db_submit-proof.php

include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['taskId'];

    // Handle Proof File Upload (Optional)
    if (isset($_FILES['proofSubmit']) && $_FILES['proofSubmit']['error'] == 0) {
        $targetDir = "../uploads/proofs/"; // Directory to store proofs
        $relativeDir = "uploads/proofs/"; // Relative directory to store in the database
        $fileName = basename($_FILES["proofSubmit"]["name"]);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uniqueFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . uniqid() . '.' . $fileExtension;
        $targetFile = $targetDir . $uniqueFileName;
        $relativeFile = $relativeDir . $uniqueFileName;

        // Validate File Type
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'];
        if (in_array($fileExtension, $allowedExtensions)) {
            if (move_uploaded_file($_FILES["proofSubmit"]["tmp_name"], $targetFile)) {
                $proof = $relativeFile; // Store relative file path in database

                // Update the proof field in the tasks table
                $stmt = $conn->prepare("UPDATE tasks SET proof = ? WHERE id = ?");
                if ($stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param('si', $proof, $taskId);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update task']);
                }

                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>