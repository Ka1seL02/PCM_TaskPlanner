<?php
session_start();
include 'db/db_connection.php';

// CHECK IF LOGGED IN ELSE REDIRECT BACK TO LOGIN PAGE
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
include 'components/admin_sidebar.php'; // Sidebar Component

// Fetch archived tasks
$sql = "SELECT * FROM tasks WHERE status = 'archived' ORDER BY archived_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Tasks</title>
    <!-- STYLES -->
    <link rel="stylesheet" href="assets/styles/root.css">
    <link rel="stylesheet" href="assets/styles/sidebar.css">
    <link rel="stylesheet" href="assets/styles/admin_dashboard.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>

<body>
    <div class="main-content">
        <h1>Archived Tasks</h1>
        <p>Below are the tasks that have been archived.</p>

        <div class="task-report archived-tbl">
            <div class="top-text">
                <h3>Task Report: <span class="text gray-text">Archived</span></h3>
            </div>
            <div class="table-container">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Intern</th>
                                <th>Task Name</th>
                                <th>Archived Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['assignedto']); ?></td>
                                    <td><?= htmlspecialchars($row['taskname']); ?></td>
                                    <td><?= date("F j, Y, g:i a", strtotime($row['archived_at'])); ?></td>
                                    <td>
                                        <button class="restore-btn" data-id="<?= $row['id']; ?>">
                                            <i class="fa-solid fa-rotate-left"></i> Restore
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <h2><i class="fa-solid fa-folder-open"></i> No archived tasks found</h2>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.restore-btn').forEach(button => {
            button.addEventListener('click', function () {
                let taskId = this.getAttribute('data-id');

                Swal.fire({
                    title: "Restore Task?",
                    text: "Are you sure you want to restore this task?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, restore!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `restore_task.php?id=${taskId}`;
                    }
                });
            });
        });
    </script>
</body>

</html>
