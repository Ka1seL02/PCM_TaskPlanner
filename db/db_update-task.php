<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['taskId'];
    $taskName = $_POST['taskName'];
    $taskDescription = $_POST['taskDescription'];
    $assignTo = $_POST['assignTo'];
    $dueTime = $_POST['dueTime'];
    $comments = !empty($_POST['taskComment']) ? $_POST['taskComment'] : null;
    $attachment = null;

    // HANDLE FILE UPLOAD
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['attachment']['tmp_name'];
        $fileName = $_FILES['attachment']['name'];
        $fileSize = $_FILES['attachment']['size'];
        $fileType = $_FILES['attachment']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = '../uploads/attachments/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $attachment = 'uploads/attachments/' . $newFileName;
        }
    }

    // UPDATE TASK IN THE DATABASE
    $sql = "UPDATE tasks SET taskname = ?, taskdescription = ?, assignedto = ?, duetime = ?, comments = ?, attachment = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssi", $taskName, $taskDescription, $assignTo, $dueTime, $comments, $attachment, $taskId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update task.']);
    }

    $stmt->close();
    $conn->close();
}
?>