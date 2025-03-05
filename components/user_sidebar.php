<?php
include 'db/db_connection.php';
// SESSION VARIABLES
$id = $_SESSION['id'];
$fullname = $_SESSION['fullname'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$school = $_SESSION['school'];
$department = $_SESSION['department'];
$position = $_SESSION['position'];
$profile_img = $_SESSION['pfp'];
?>
<!-- SWEETALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="sidebar">
    <div class="logo">
        <img src="assets/sidebar_logo.jpg">
        <h1>Task<span>Planner</span></h1>
    </div>
    <nav>
        <ul>
            <li><a href="user_dashboard.php" class="<?php echo $current_page == 'user_dashboard.php' ? 'active' : ''; ?>"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
            <li><a href="user_tasklist.php" class="<?php echo $current_page == 'user_tasklist.php' ? 'active' : ''; ?>"><i class="fa-solid fa-tasks"></i>Your Tasks</a></li>
            <li><a href="user_profile.php" class="<?php echo $current_page == 'user_profile.php' ? 'active' : ''; ?>"><i class="fa-solid fa-user"></i>User Profile</a></li>
        </ul>
    </nav>
    <div class="admin-profile">
        <div class="admin-info">
            <img src="<?php echo htmlspecialchars($profile_img); ?>">
            <div>
                <h3><?php echo htmlspecialchars($username); ?></h3>
                <small><?php echo htmlspecialchars($department . ' Department'); ?></small>
            </div>
        </div>
        <button id="logout-btn">Logout</button>
    </div>
</div>
<script>
    document.getElementById("logout-btn").addEventListener("click", function() {
        Swal.fire({
            title: "Are you sure?",
            text: "You will be logged out!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, logout!"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    });
</script>