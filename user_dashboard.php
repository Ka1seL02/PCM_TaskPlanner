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
    <title>User Dashboard</title>
    <!-- PAGE STYLES -->
    <link rel="stylesheet" href="assets/styles/root.css">
    <link rel="stylesheet" href="assets/styles/sidebar.css">
    <link rel="stylesheet" href="assets/styles/user_dashboard.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    include 'components/user_sidebar.php';
    ?>
    <div class="main-content">
        <!-- USER CARD PROFILE DISPLAY -->
        <div class="user-card">
            <div class="user-profile">
                <h2>Welcome</h2>
                <h1><span><?php echo htmlspecialchars($fullname); ?></span>!</h1>
                <p>Welcome back! Stay productive and conquer your tasks today. 🚀</p>
            </div>
            <img class="profile-img" src="<?php echo htmlspecialchars($profile_img); ?>">
        </div>
        <div class="utilities">
            <!-- USER TASK COUNT PER STATUS CARDS -->
            <div class="task-info">
                <?php
                function getTaskCount($conn, $id, $status)
                {
                    $sql = "SELECT COUNT(*) AS count FROM tasks WHERE assignedto = ? AND status = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $id, $status);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    return $row['count'];
                }
                $pending_count = getTaskCount($conn, $id, "Pending");
                $due_count = getTaskCount($conn, $id, "In-Progress");
                $missed_count = getTaskCount($conn, $id, "Missed");
                ?>
                <!-- CARDS -->
                <div class="stats-card">
                    <div class="pending-tasks card">
                        <i class="fa-solid fa-square-check"></i>
                        <h3>Pending</h3>
                        <h1><?php echo $pending_count; ?></h1>
                    </div>
                    <div class="due-tasks card">
                        <i class="fa-solid fa-list"></i>
                        <h3>Due</h3>
                        <h1><?php echo $due_count; ?></h1>
                    </div>
                    <div class="missed-tasks card">
                        <i class="fa-solid fa-rectangle-xmark"></i>
                        <h3>Overdue</h3>
                        <h1><?php echo $missed_count; ?></h1>
                    </div>
                </div>
            </div>
            <!-- CALENDAR -->
            <div class="calendar-section">
                <iframe
                    src="https://calendar.google.com/calendar/embed?src=en.philippines%23holiday%40group.v.calendar.google.com"
                    class="calendar" frameborder="0">
                </iframe>
            </div>
        </div>
    </div>
</body>

</html>