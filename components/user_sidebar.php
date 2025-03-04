<?php
session_start();
include 'db/db_connection.php';
// Session variables
$id = $_SESSION['id'];
$fullname = $_SESSION['fullname'];
$username = $_SESSION['username'];
$school = $_SESSION['school'];
$department = $_SESSION['department'];
$position = $_SESSION['position'];
$profile_img = $_SESSION['pfp'];
?>

<div class="sidebar">
    <!-- Logo !-->
    <div class="logo">
        <img src="assets/sidebar_logo.jpg">
        <h1>Task<span>Planner</span></h1>
    </div>
    <!-- Sidebar Navigation !-->
    <nav>
        <ul>
            <li><a href="user_dashboard.php" class="<?php echo $current_page == 'user_dashboard.php' ? 'active' : ''; ?>"><i class="fa-solid fa-table-columns"></i> Dashboard</a></li>
            <li><a href="user_tasklist.php" class="<?php echo $current_page == 'user_tasklist.php' ? 'active' : ''; ?>"><i class="fa-solid fa-tasks"></i>Your Tasks</a></li>
            <li><a href="" class="<?php echo $current_page == '' ? 'active' : ''; ?>"><i class="fa-solid fa-user"></i>User Profile</a></li>
        </ul>
    </nav>
    <!-- Small Box Profile !-->
    <div class="admin-profile">
        <div class="admin-info">
            <img src="<?php echo htmlspecialchars($profile_img); ?>">
            <div>
                <h3><?php echo htmlspecialchars($username); ?></h3>
                <small><?php echo htmlspecialchars($department . ' Department'); ?></small>
            </div>
        </div>
        <button id="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</div>