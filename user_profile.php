<?php
session_start();
// CHECK IF LOGGED IN ELSE REDIRECT BACK TO LOGIN PAGE
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- PAGE STYLES -->
    <link rel="stylesheet" href="assets/styles/root.css">
    <link rel="stylesheet" href="assets/styles/sidebar.css">
    <link rel="stylesheet" href="assets/styles/user_profile.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    include 'components/user_sidebar.php';
    ?>
    <div class="main-content">
        <h1>Profile</h1>
        <p>Your profile, your productivity hub.</p>
        <div class="navbar">
            <a href="#" class="nav-link active" data-target="profile-settings">Profile Settings</a>
            <a href="#" class="nav-link" data-target="change-password">Change Password</a>
        </div>
        <div id="profile-settings" class="content-section active">
            <img src="<?php echo htmlspecialchars($profile_img); ?>">
            <form method="POST" enctype="multipart/form-data">

                <label>Upload Profile Picture</label>
                <input type="file" name="profile_img">

                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>

                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label>School</label>
                <input type="text" name="school" value="<?php echo htmlspecialchars($school); ?>" required>

                <label for="department">Choose a Department:</label>
                <select name="department" id="department">
                    <option value="Marketing" <?php if ($department == 'Marketing') echo 'selected'; ?>>Marketing</option>
                    <option value="Admin" <?php if ($department == 'Admin') echo 'selected'; ?>>Admin</option>
                    <option value="IT" <?php if ($department == 'IT') echo 'selected'; ?>>IT</option>
                    <option value="HR" <?php if ($department == 'HR') echo 'selected'; ?>>HR</option>
                </select>

                <label>Position</label>
                <input type="text" name="position" value="<?php echo htmlspecialchars($position); ?>" readonly required>

                <div class="modal-buttons">
                    <button type="button">Edit Profile</button>
                    <button type="submit">Save Changes</button>
                </div>
            </form>
        </div>

        <div id="change-password" class="content-section">
            <h2>Change Password</h2>
            <form method="POST">
                <!-- NEW PASSWORD -->
                <div class="password-wrapper">
                    <input type="password" name="new_password" required id="register-password">
                    <span class="toggle-password" data-target="register-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>

                <!-- NEW PASSWORD -->
                <div class="password-wrapper">
                    <input type="password" name="new_password" required id="register-password">
                    <span class="toggle-password" data-target="register-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
                <!-- CONFIRM NEW PASSWORD -->
                <div class="password-wrapper">
                    <input type="password" name="confirm_new_password" required id="confirm-password">
                    <span class="toggle-password" data-target="confirm-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>

                <span class="error-message" id="login-error"></span>

                <div class="modal-buttons">
                    <button type="button">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</body>
<script>
    // For switching class
    document.addEventListener("DOMContentLoaded", function() {
        const navLinks = document.querySelectorAll(".nav-link");
        const contentSections = document.querySelectorAll(".content-section");

        navLinks.forEach(link => {
            link.addEventListener("click", function(event) {
                event.preventDefault();
                document.querySelector(".nav-link.active")?.classList.remove("active");
                this.classList.add("active");

                const target = this.getAttribute("data-target");
                contentSections.forEach(section => {
                    if (section.id === target) {
                        section.style.display = 'flex';
                    } else {
                        section.style.display = 'none';
                    }
                });
            });
        });

        // Initially hide all content sections except the active one
        contentSections.forEach(section => {
            if (!section.classList.contains('active')) {
                section.style.display = 'none';
            }
        });
    });
</script>

</html>