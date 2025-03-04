<?php
include 'db_connection.php';

// Handles form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and escape input data
    $fullname   = trim($_POST['fullname']);
    $username   = trim($_POST['username']);
    $school     = trim($_POST['school']);
    $email      = trim($_POST['email']);
    $department = trim($_POST['department']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    // Set fixed values
    $position = 'Intern';
    $pfp = null;

    // Check if email already exists
    $check_user = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check_user->bind_param("ss", $email, $username);
    $check_user->execute();
    $result = $check_user->get_result();

    if ($result->num_rows > 0) {
        echo "Email or username already registered!";
        exit();
    }

    // Handle Profile Picture Upload (Optional)
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $targetDir = "../uploads/profile/"; // Directory to store images
        $relativeDir = "uploads/profile/"; // Relative directory to store in the database
        $fileName = basename($_FILES["profileImage"]["name"]);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $uniqueFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . uniqid() . '.' . $fileExtension;
        $targetFile = $targetDir . $uniqueFileName;
        $relativeFile = $relativeDir . $uniqueFileName;

        // Validate Image
        $check = getimagesize($_FILES["profileImage"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
                $pfp = $relativeFile; // Store relative file path in database
            } else {
                echo "Error uploading profile image.";
                exit;
            }
        } else {
            echo "File is not a valid image.";
            exit;
        }
    }

    // Prepare the INSERT statement (using prepared statements for security)
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, school, email, department, position, password, pfp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ssssssss", $fullname, $username, $school, $email, $department, $position, $password, $pfp);

    if ($stmt->execute()) {
        echo "Account created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $check_user->close();
}
$conn->close();
?>