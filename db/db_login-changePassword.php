<?php 
include 'db_connection.php';

// Get Email from POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        echo "Email is required";
        exit;
    }
    // Check if the email exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "No account found with that email.";
        exit();
    }
    // Hash TEMP password
    $newpassword = '$2y$10$wReodLUqPJuROSOh3f4VRepEYgkIHYk5YgdFQDS0n6vf9RF.Jk7L.';
    // Update the password where email matches
    $sql = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $newpassword, $email);
    
    if ($stmt->execute()) {
        echo "Successfully changed password.";
    } else {
        echo "Error updating password: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>