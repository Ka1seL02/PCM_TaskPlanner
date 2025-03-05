<?php
header('Content-Type: application/json');

include 'db_connection.php'; // Ensure you include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $school = $_POST['school'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $password = password_hash('temp1234', PASSWORD_DEFAULT); // Hash the default password

    // Log the received data
    error_log("Received data: fullname=$fullname, username=$username, email=$email, school=$school, position=$position, department=$department");

    // Handle profile image upload
    $profileImage = '';
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['profileImage']['tmp_name'];
        $imageName = $_FILES['profileImage']['name'];
        $imageSize = $_FILES['profileImage']['size'];
        $imageType = $_FILES['profileImage']['type'];
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        // Validate image file type
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid image file type']);
            exit();
        }

        // Move the uploaded file to the desired directory
        $uploadDir = '../uploads/profile/';
        $profileImage = $uploadDir . uniqid() . '.' . $imageExtension;
        if (!move_uploaded_file($imageTmpPath, $profileImage)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload profile image']);
            exit();
        }

        // Set the path to be stored in the database
        $profileImage = 'uploads/profile/' . basename($profileImage);
    }

    // Insert user into the database
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, school, position, department, password, pfp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        error_log("Failed to prepare statement: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit();
    }
    $stmt->bind_param('ssssssss', $fullname, $username, $email, $school, $position, $department, $password, $profileImage);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'User account created successfully']);
    } else {
        error_log("Failed to create user account: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to create user account']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>