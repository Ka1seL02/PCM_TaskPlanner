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
            <img src="<?php echo htmlspecialchars($_SESSION['pfp']); ?>">
            <form id="profileForm" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <input type="hidden" name="current_profile_img" value="<?php echo htmlspecialchars($profile_img); ?>">

                <label>Upload Profile Picture</label>
                <input type="file" name="profile_img" disabled>

                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($_SESSION['fullname']); ?>"
                    required disabled>

                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>"
                    required disabled>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required
                    disabled>

                <label>School</label>
                <input type="text" name="school" value="<?php echo htmlspecialchars($_SESSION['school']); ?>" required
                    disabled>

                <label for="department">Choose a Department:</label>
                <select name="department" id="department" disabled>
                    <option value="Marketing" <?php if ($_SESSION['department'] == 'Marketing')
                        echo 'selected'; ?>>
                        Marketing</option>
                    <option value="Admin" <?php if ($_SESSION['department'] == 'Admin')
                        echo 'selected'; ?>>Admin</option>
                    <option value="IT" <?php if ($_SESSION['department'] == 'IT')
                        echo 'selected'; ?>>IT</option>
                    <option value="HR" <?php if ($_SESSION['department'] == 'HR')
                        echo 'selected'; ?>>HR</option>
                </select>

                <label>Position</label>
                <input type="text" name="position" value="<?php echo htmlspecialchars($_SESSION['position']); ?>"
                    readonly required disabled>

                <div class="modal-buttons">
                    <button type="button" id="editProfileButton">Edit Profile</button>
                    <button type="button" id="cancelButton" hidden>Cancel</button>
                    <button type="submit" id="saveChangesButton" hidden>Save Changes</button>
                </div>
            </form>
        </div>

        <div id="change-password" class="content-section">
            <h2>Change Password</h2>
            <form method="POST">
                <!-- NEW PASSWORD -->
                <label>Current Password</label>
                <div class="password-wrapper">
                    <input type="password" name="current_password" required id="current-password">
                    <span class="toggle-password" data-target="current-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>

                <!-- NEW PASSWORD -->
                <label>New Password</label>
                <div class="password-wrapper">
                    <input type="password" name="new_password" required id="new-password">
                    <span class="toggle-password" data-target="new-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
                <!-- CONFIRM NEW PASSWORD -->
                <label>Confirm New Password</label>
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
    document.addEventListener("DOMContentLoaded", function () {
        const navLinks = document.querySelectorAll(".nav-link");
        const contentSections = document.querySelectorAll(".content-section");

        navLinks.forEach(link => {
            link.addEventListener("click", function (event) {
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

        // Enable form inputs and buttons when "Edit Profile" is clicked
        const editProfileButton = document.getElementById('editProfileButton');
        const cancelButton = document.getElementById('cancelButton');
        const saveChangesButton = document.getElementById('saveChangesButton');
        const formInputs = document.querySelectorAll('#profile-settings input, #profile-settings select');

        editProfileButton.addEventListener('click', function () {
            formInputs.forEach(input => input.disabled = false);
            saveChangesButton.hidden = false;
            cancelButton.hidden = false;
            editProfileButton.hidden = true;
        });

        // Reload the page when "Cancel" is clicked
        cancelButton.addEventListener('click', function () {
            location.reload();
        });

        // Handle form submission via AJAX with SweetAlert confirmation
        const profileForm = document.getElementById('profileForm');
        profileForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(profileForm);
            // SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to save the changes?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'No, cancel!',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('db/db_update-profile.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => {
                            // First get the text to debug
                            return response.text().then(text => {
                                console.log('Server response:', text);
                                // Now try to parse as JSON
                                try {
                                    return JSON.parse(text);
                                } catch (e) {
                                    console.error('Failed to parse JSON:', e);
                                    throw new Error('Invalid response format');
                                }
                            });
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Saved!',
                                    'Your profile has been updated.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error!',
                                'An error occurred while updating your profile.',
                                'error'
                            );
                        });
                }
            });
        });
    });
</script>

</html>