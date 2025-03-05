<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskName = $_POST['taskName'];
    $taskDescription = $_POST['taskDescription'];
    $assignTo = $_POST['assignTo'];
    $startTime = $_POST['startTime'];
    $dueTime = $_POST['dueTime'];
    $status = 'Pending';
    $proof = null;

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

    // Insert task into the database
    $query = "INSERT INTO tasks (taskname, taskdescription, assignedto, starttime, duetime, status, attachment, proof) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssisssss', $taskName, $taskDescription, $assignTo, $startTime, $dueTime, $status, $attachment, $proof);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>