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
    <title>Admin Dashboard</title>
    <!-- STYLES -->
    <link rel="stylesheet" href="assets/styles/root.css">
    <link rel="stylesheet" href="assets/styles/sidebar.css">
    <link rel="stylesheet" href="assets/styles/admin_dashboard.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
include 'components/admin_sidebar.php'; // SIDEBAR COMPONENT
include 'db/db_admin-dashboard.php'; // CONTAINS THE FETCHING OF ITEMS TO DISPLAY AND COUNTING NO. OF INTERNS PER DEPARTMENT
?>
<div class="main-content">
    <h1>Dashboard</h1>
    <p>Welcome <?php echo $position; ?>! Here's an overview of today's progress.</p>
    <!-- CARDS DISPLAYING NO. OF STUDENTS PER DEPARTMENT -->
    <div class="stats-card">
        <?php if ($position === "Admin"): ?>
            <!-- ADMIN SEES ALL CARD -->
            <div class="card">
                <h3>IT Department</h3>
                <h1><?php echo $internCounts['IT']; ?></h1>
                <i class="fa-solid fa-computer"></i>
            </div>
            <div class="card">
                <h3>Human Resource Department</h3>
                <h1><?php echo $internCounts['HR']; ?></h1>
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="card">
                <h3>Marketing Department</h3>
                <h1><?php echo $internCounts['Marketing']; ?></h1>
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="card">
                <h3>Admin Department</h3>
                <h1><?php echo $internCounts['Admin']; ?></h1>
                <i class="fa-solid fa-briefcase"></i>
            </div>
        <?php elseif ($position === "Supervisor"): ?>
            <!-- SUPERVISOR ONLY SEE CARDS OF THEIR DEPARTMENT -->
            <?php if ($department === "IT"): ?>
                <div class="card">
                    <h3>IT Department</h3>
                    <h1><?php echo $internCounts['IT']; ?></h1>
                    <i class="fa-solid fa-computer"></i>
                </div>
            <?php elseif ($department === "HR"): ?>
                <div class="card">
                    <h3>Human Resource Department</h3>
                    <h1><?php echo $internCounts['HR']; ?></h1>
                    <i class="fa-solid fa-users"></i>
                </div>
            <?php elseif ($department === "Marketing"): ?>
                <div class="card">
                    <h3>Marketing Department</h3>
                    <h1><?php echo $internCounts['Marketing']; ?></h1>
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            <?php elseif ($department === "Admin"): ?>
                <div class="card">
                    <h3>Admin Department</h3>
                    <h1><?php echo $internCounts['Admin']; ?></h1>
                    <i class="fa-solid fa-briefcase"></i>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- TABLES FOR DISPLAYING TASK FOR EACH STATUSES -->
    <!-- IN PROGRESS TABLE -->
    <div class="task-report in-progress-tbl">
        <div class="top-text">
            <h3>Task Report: <span class="text blue-text">In Progress</span></h3>
        </div>
        <div class="table-container">
            <?php if (!empty($paginatedTasksInProgress)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Interns</th>
                            <th>Task</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = $offsetInProgress + 1;
                        foreach ($paginatedTasksInProgress as $task): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($task['assigned_to']); ?></td>
                                <td><?= htmlspecialchars($task['taskname']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h2><i class="fa-solid fa-folder-open"></i> No data found</h2>
            <?php endif; ?>
        </div>
        <!-- PAGENATION -->
        <div class="pagination">
            <?php if ($totalPagesInProgress > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPagesInProgress; $i++): ?>
                        <a href="?page_in_progress=<?= $i ?>" class="<?= ($i == $currentPageInProgress) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- PENDING TABLE -->
    <div class="task-report pending-tbl">
        <div class="top-text">
            <h3>Task Report: <span class="text orange-text">Pending</span></h3>
        </div>
        <div class="table-container">
            <?php if (!empty($paginatedTasksPending)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Interns</th>
                            <th>Task</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offsetPending + 1;
                        foreach ($paginatedTasksPending as $task): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($task['assigned_to']); ?></td>
                                <td><?= htmlspecialchars($task['taskname']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h2><i class="fa-solid fa-folder-open"></i>No data found</h2>
            <?php endif; ?>
        </div>
        <!-- PAGENATION -->
        <div class="pagination">
            <?php if ($totalPagesPending > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPagesPending; $i++): ?>
                        <a href="?page_pending=<?= $i ?>" class="<?= ($i == $currentPagePending) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- MISSED TABLE -->
    <div class="task-report missed-tbl">
        <div class="top-text">
            <h3>Task Report: <span class="text red-text">Missed</span></h3>
        </div>
        <div class="table-container">
            <?php if (!empty($paginatedTasksMissed)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Interns</th>
                            <th>Task</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offsetMissed + 1;
                        foreach ($paginatedTasksMissed as $task): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($task['assigned_to']); ?></td>
                                <td><?= htmlspecialchars($task['taskname']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h2><i class="fa-solid fa-folder-open"></i>No data found</h2>
            <?php endif; ?>
        </div>
        <!-- PAGENATION -->
        <div class="pagination">
            <?php if ($totalPagesMissed > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPagesMissed; $i++): ?>
                        <a href="?page_missed=<?= $i ?>" class="<?= ($i == $currentPageMissed) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- COMPLETED TABLE -->
    <div class="task-report completed-tbl">
        <div class="top-text">
            <h3>Task Report: <span class="text green-text">Completed</span></h3>
        </div>
        <div class="table-container">
            <?php if (!empty($paginatedTasksCompleted)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Interns</th>
                            <th>Task</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = $offsetCompleted + 1;
                        foreach ($paginatedTasksCompleted as $task): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($task['assigned_to']); ?></td>
                                <td><?= htmlspecialchars($task['taskname']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h2><i class="fa-solid fa-folder-open"></i>No data found</h2>
            <?php endif; ?>
        </div>
        <!-- PAGENATION -->
        <div class="pagination">
            <?php if ($totalPagesCompleted > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPagesCompleted; $i++): ?>
                        <a href="?page_completed=<?= $i ?>" class="<?= ($i == $currentPageCompleted) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>

</html>