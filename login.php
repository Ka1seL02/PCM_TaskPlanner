<html lang="en">
<head>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <!-- STYLES -->
        <link rel="stylesheet" href="assets/styles/root.css">
        <link rel="stylesheet" href="assets/styles/login.css">
        <!-- ICONS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
</head>

<body>
    <div class="logo-container">
        <img src="assets/login_fragranza-logo.png">
        <img src="assets/login_pcm-logo.png">
    </div>

    <!-- LOGIN MODAL -->
    <div class="login-modal">
        <div class="login-box">
            <h2>Login</h2>
            <p>Enter your account details.</p>

            <form class="login-form" id="loginForm">
                <input type="text" name="username" placeholder="Username" required>
                <div class="password-wrapper">
                    <input type="password" name="password" placeholder="Password" required id="login-password">
                    <span class="toggle-password" data-target="login-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
                <span class="error-message" id="login-error"></span>
                <button type="submit" id="login-btn">LOGIN</button>
            </form>

            <div class="login-options">
                <a href="#" class="forgot-link">Forgot Password?</a>
                <span> | </span>
                <a href="#" class="create-link">Create Account</a>
            </div>
        </div>
    </div>

    <!-- FOROGOT PASSWORD MODAL -->
    <div class="forgot-password-modal">
        <div class="forgot-password-box">
            <h2>Forgot Password</h2>
            <p>Enter your registered email to reset your password.</p>
            <form class="forgot-password-form" id="forgotPasswordForm">
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit" id="resetpasswordBtn">RESET PASSWORD</button>
                <span class="error-message" id="forgot-password-error"></span>
            </form>
            <div class="login-options">
                <a href="#" class="login-link">Back to Login</a>
                <span> | </span>
                <a href="#" class="create-link">Create Account</a>
            </div>
        </div>
    </div>

    <!-- CREATE/REGISTER ACCOUNT MODAL -->
    <div class="register-modal">
        <div class="register-box">
            <h2>Create Account</h2>
            <p>Fill in your details below.</p>
            <!-- REGISTER FORM-->
            <form class="register-form" id="registerForm">
                <input type="text" name="fullname" placeholder="Full Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <span class="error-message" id="email-user-error"></span>
                <input type="text" name="school" placeholder="School" required>
                <select name="department" required>
                    <option value="">Select Department</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Admin">Admin</option>
                    <option value="IT">IT</option>
                    <option value="HR">HR</option>
                </select>
                <!-- PASSWORD -->
                <div class="password-wrapper">
                    <input type="password" name="password" placeholder="Password" required id="register-password">
                    <span class="toggle-password" data-target="register-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
                <!-- CONFIRM PASSWORD -->
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required id="confirm-password">
                    <span class="toggle-password" data-target="confirm-password">
                        <i class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
                <span class="error-message" id="pass-error"></span>
                <input type="file" id="profileImage" name="profileImage" accept="image/*">

                <button type="submit" name="register" id="register-btn">CREATE ACCOUNT</button>
            </form>
            <!-- NAV TO OTHER MODALS -->
            <div class="login-options">
                <a href="#" class="login-link">Already have an account?</a>
            </div>
        </div>
    </div>
    <!-- SCRIPTS -->
    <script src="scripts/login.js"></script>
</html>