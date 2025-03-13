<?php
// Include database connection
include 'db_connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Turn on error handling
ini_set('display_errors', 0);
error_reporting(0);

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate required fields
        if (!isset($_POST['id']) || !isset($_POST['fullname']) || !isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['school']) || !isset($_POST['department'])) {
            throw new Exception('Missing required fields');
        }

        $id = $_POST['id'];
        $new_fullname = $_POST['fullname'];
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $new_school = $_POST['school'];
        $new_department = $_POST['department'];
        $profile_img = $_POST['current_profile_img'];

        // Handle profile image upload
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_img']['tmp_name'];
            $fileName = $_FILES['profile_img']['name'];
            $fileSize = $_FILES['profile_img']['size'];
            $fileType = $_FILES['profile_img']['type'];
            
            // Validate file type
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, PNG and GIF are allowed.');
            }
            
            // Generate new filename to prevent overwriting
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = '../uploads/profile_pics/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_img = 'uploads/profile_pics/' . $newFileName;
            } else {
                throw new Exception('Failed to upload profile image.');
            }
        }

        // Check if username already exists (skip if username hasn't changed)
        $checkUsername = "SELECT id FROM users WHERE username = ? AND id != ?";
        $stmt = $conn->prepare($checkUsername);
        
        // Check if prepare was successful
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        
        $stmt->bind_param("si", $new_username, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Username already exists. Please choose a different one.');
        }
        $stmt->close();
        
        // Check if email already exists (skip if email hasn't changed)
        $checkEmail = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($checkEmail);
        
        // Check if prepare was successful
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        
        $stmt->bind_param("si", $new_email, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Email already exists. Please choose a different one.');
        }
        $stmt->close();

        // UPDATE USER IN THE DATABASE
        $sql = "UPDATE users SET fullname = ?, username = ?, email = ?, school = ?, department = ?, pfp = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        // Check if prepare was successful
        if ($stmt === false) {
            throw new Exception('Database error: ' . $conn->error);
        }
        
        $stmt->bind_param("ssssssi", $new_fullname, $new_username, $new_email, $new_school, $new_department, $profile_img, $id);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            throw new Exception('Failed to update profile: ' . $stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception('Invalid request method. Only POST is allowed.');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// Close the database connection if it exists
if (isset($conn)) {
    $conn->close();
}

// Return JSON response
echo json_encode($response);
exit;
?>