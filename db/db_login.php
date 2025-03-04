<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if the username or email exists
    $query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo "Database error: " . $conn->error;
        exit();
    }
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows === 0) {
        echo "User does not exist.";
        exit();
    }

    $row = $result->fetch_assoc();

    // Verify the password
    if (password_verify($password, $row['password'])) {
        // Set session variables
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['school'] = $row['school'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['department'] = $row['department'];
        $_SESSION['position'] = $row['position'];
        $_SESSION['pfp'] = $row['pfp'];

        if($_SESSION['position'] == 'Intern') {   
            echo "Intern Login successful.";
        } else {
            echo "Admin/Supervisor Login successful.";
        }
    } else {
        echo "Password is incorrect.";
    }
    $stmt->close();
}
$conn->close();
?>