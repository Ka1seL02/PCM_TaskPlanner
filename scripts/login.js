document.addEventListener('DOMContentLoaded', function () {
    // MODALS
    const loginModal = document.querySelector('.login-modal');
    const forgotPasswordModal = document.querySelector('.forgot-password-modal');
    const registerModal = document.querySelector('.register-modal');
    // BUTTONS
    const loginLinks = document.querySelectorAll('.login-link');
    const forgotLinks = document.querySelectorAll('.forgot-link');
    const createLinks = document.querySelectorAll('.create-link');
    // HIDE ALL MODALS
    function hideAllModals() {
        loginModal.style.display = 'none';
        forgotPasswordModal.style.display = 'none';
        registerModal.style.display = 'none';
    }
    // LINKS TO CHANGE DISPLAYED MODAL
    loginLinks.forEach(function (loginLink) {
        loginLink.addEventListener('click', function (e) {
            hideAllModals();
            loginModal.querySelector('form').reset();
            loginModal.style.display = 'flex';
        });
    });
    forgotLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            hideAllModals();
            forgotPasswordModal.querySelector('form').reset();
            forgotPasswordModal.style.display = 'flex';
        });
    });
    createLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            hideAllModals();
            registerModal.querySelector('form').reset();
            registerModal.style.display = 'flex';
        });
    });
});

// TOGGLE PASSWORD VISIBILITY
document.querySelectorAll('.toggle-password').forEach(toggle => {
    toggle.addEventListener('click', function() {
        var targetInput = document.getElementById(this.getAttribute('data-target'));
        var icon = this.querySelector('i');

        // Toggle password visibility
        if (targetInput.type === 'password') {
            targetInput.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            targetInput.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    });
});

// LOGIN ACCOUNT
document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    document.getElementById('login-error').textContent = "";
    const formData = new FormData(this);
    fetch('db/db_login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            if (result.trim() === "Intern Login successful.") {
                alert(result);
                window.location.href = 'user_dashboard.php';
            } else if (result.trim() === "Admin/Supervisor Login successful.") {
                alert(result);
                window.location.href = 'admin_dashboard.php';
            } else {
                document.getElementById('login-error').textContent = result;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('login-error').textContent = "An error occurred. Please try again.";
        });
});

// RESET PASSWORD
document.getElementById('forgotPasswordForm').addEventListener('submit', function (e) {
    e.preventDefault();
    document.getElementById('forgot-password-error').textContent = "";
    const formData = new FormData(this);
    fetch('db/db_forgot-password.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(result => {
            if (result.trim() === "Successfully changed password.") {
                alert(result);
                window.location.reload();
            } else {
                document.getElementById('forgot-password-error').textContent = result;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('forgot-password-error').textContent = "An error occurred. Please try again.";
        });
});

// REGISTER INTERNS
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // CLEAR ANY PREVIOUS ERROR MESSAGES
    document.getElementById('email-user-error').textContent = "";
    document.getElementById('pass-error').textContent = "";
    const formData = new FormData(this);
    let password = formData.get('password');
    let confirmPassword = formData.get('confirm_password');
    // CHECK IF PASSWORD MATCH
    if (password !== confirmPassword) {
        document.getElementById('pass-error').textContent = "Passwords do not match!";
        return;
    }
    // SEND FORM DATA TO INSERT TO 'users' TABLE
    fetch('db/db_register-acc.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        console.log(result);
        if(result.trim() === "Account created successfully.") {
            alert(result);
            document.getElementById('registerForm').reset();
            window.location.reload();
        } else {
            document.getElementById('email-user-error').textContent = result;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('email-user-error').textContent = "An error occurred. Please try again.";
    });
});