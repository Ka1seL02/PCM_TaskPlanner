<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['taskId'];
    $taskName = $_POST['taskName'];
    $taskDescription = $_POST['taskDescription'];
    $assignTo = $_POST['assignTo'];
    $startTime = $_POST['startTime'];
    $dueTime = $_POST['dueTime'];

    // Log received data for debugging
    error_log("Received data: taskId=$taskId, taskName=$taskName, taskDescription=$taskDescription, assignTo=$assignTo, startTime=$startTime, dueTime=$dueTime");

    // Handle file upload
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['attachment']['tmp_name'];
        $fileName = $_FILES['attachment']['name'];
        $fileSize = $_FILES['attachment']['size'];
        $fileType = $_FILES['attachment']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Check if the file type is allowed
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory in which the uploaded file will be moved
            $uploadFileDir = '../uploads/attachments/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $attachment = 'uploads/attachments/' . $newFileName;
            }
        }
    }

    // Update the task in the database
    if ($attachment) {
        $query = "UPDATE tasks SET taskname = ?, taskdescription = ?, assignedto = ?, starttime = ?, duetime = ?, attachment = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssisssi', $taskName, $taskDescription, $assignTo, $startTime, $dueTime, $attachment, $taskId);
    } else {
        $query = "UPDATE tasks SET taskname = ?, taskdescription = ?, assignedto = ?, starttime = ?, duetime = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssissi', $taskName, $taskDescription, $assignTo, $startTime, $dueTime, $taskId);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        error_log('Failed to update task: ' . $stmt->error);
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>