<?php
session_start();
// CHECK IF LOGGED IN ELSE REDIRECT BACK TO LOGIN PAGE
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db/db_admin-archived.php'; // Include the logic for fetching archived tasks
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
    <link rel="stylesheet" href="assets/styles/admin_reports.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    include 'components/admin_sidebar.php';
    ?>
    <div class="main-content">
        <h1>Archived Tasks</h1>
        <p>View and manage archived tasks. You may restore tasks if needed.</p>
        <!-- CONTROLS (SEARCH, REFRESH, SORT DROPDOWN) -->
        <div class="filter-controls">
            <div class="left-section">
                <!-- REFRESH -->
                <button id="refreshButton"><i class="fa fa-refresh"></i></button>
                <!-- SEARCH -->
                <div class="search-bar">
                    <i class="fa fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search task name..."
                        value="<?= htmlspecialchars($search_filter) ?>">
                </div>
                <button id="searchButton">Search</button>
            </div>
        </div>

        <!-- TABLE DISPLAY TASKS -->
        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Assigned To</th>
                            <th>Given</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th>Attachment</th>
                            <th>Proof</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($task = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['taskname']); ?></td>
                                <td><?= htmlspecialchars($task['assignedto']); ?></td>
                                <td><?= date("F j, Y, g:i A", strtotime($task['starttime'])); ?></td>
                                <td><?= date("F j, Y, g:i A", strtotime($task['duetime'])); ?></td>
                                <td>
                                    <span class="gray-text text"><?= htmlspecialchars($task['status']); ?></span>
                                </td>
                                <td>
                                    <?= !empty($task['attachment']) ? '<a href="' . htmlspecialchars($task['attachment'], ENT_QUOTES, 'UTF-8') . '" target="_blank" class="blue-text text"><i class="fa-solid fa-paperclip"></i> Attachment</a>' : '<span class="gray-text text"><i class="fa-solid fa-xmark"></i> No Attachment</span>'; ?>
                                </td>
                                <td>
                                    <?= !empty($task['proof']) ? '<a href="' . htmlspecialchars($task['proof'], ENT_QUOTES, 'UTF-8') . '" target="_blank" class="blue-text text"><i class="fa-solid fa-paperclip"></i> Proof</a>' : '<span class="gray-text text"><i class="fa-solid fa-xmark"></i> No Proof</span>'; ?>
                                </td>
                                <td>
                                    <button class="action-btn viewTaskButton" data-id="<?= $task['id'] ?>">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button class="action-btn restoreTaskButton" data-id="<?= $task['id'] ?>">
                                        <i class="fa fa-undo"></i>
                                    </button>
                                    <button class="action-btn deleteTaskButton" data-id="<?= $task['id'] ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h2 class="no-data"><i class="fa-solid fa-folder-open"></i> No data found</h2>
            <?php endif; ?>
        </div>
        <!-- PAGINATION -->
        <?php if ($totalTasks > $itemsPerPage): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= $search_filter ?>"
                        class="<?= ($i == $currentPage) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <!-- TASK DETAILS MODAL -->
        <div id="detailsModal" class="modal-overlay">
            <div class="modal-container">
                <h2>Task Details</h2>
                <div class="task-info">
                    <p><strong>Title:</strong> <span id="taskTitle"></span></p>
                    <p><strong>Description:</strong> <span id="task_Description"></span></p>
                    <p><strong>Start:</strong> <span id="taskStart"></span></p>
                    <p><strong>End:</strong> <span id="taskEnd"></span></p>
                    <p><strong>Assigned To:</strong> <span id="taskAssignedTo"></span></p>
                    <p><strong>Status:</strong> <span id="taskStatus"></span></p>
                    <p><strong>Task Image / File:</strong> <span id="taskFile"></span></p>
                    <p><strong>Proof Image / File:</strong> <span id="proofFile"></span></p>
                    <p><strong>Comment:</strong> <span id="taskComments"></span></p>
                </div>
                <div class="modal-buttons">
                    <button onclick="closeModalDetails('detailsModal')">Close</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handle refresh button click
            document.getElementById('refreshButton').addEventListener('click', function() {
                window.location.href = 'admin_archived.php';
            });

            // Handle search button click
            document.getElementById('searchButton').addEventListener('click', function() {
                const searchInput = document.getElementById('searchInput').value;
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('search', searchInput);
                currentUrl.searchParams.set('page', 1); // Reset to the first page
                window.location.href = currentUrl.toString();
            });

            // FETCH TASK DETAILS TO DISPLAY
            document.querySelectorAll('.viewTaskButton').forEach(button => {
                button.addEventListener('click', function() {
                    let taskId = this.getAttribute('data-id');
                    fetch('db/db_view-task.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'task_id=' + taskId
                        })
                        .then(response => response.text())
                        .then(text => {
                            console.log(text);
                            try {
                                const data = JSON.parse(text);
                                if (data.error) {
                                    alert('Task not found');
                                } else {
                                    document.getElementById('taskTitle').textContent = data.task_name;
                                    document.getElementById('task_Description').textContent = data.task_description;
                                    const startTime = new Date(data.start_time).toLocaleString('en-US', {
                                        month: 'long',
                                        day: 'numeric',
                                        year: 'numeric',
                                        hour: 'numeric',
                                        minute: 'numeric',
                                        hour12: true
                                    });
                                    const dueTime = new Date(data.due_time).toLocaleString('en-US', {
                                        month: 'long',
                                        day: 'numeric',
                                        year: 'numeric',
                                        hour: 'numeric',
                                        minute: 'numeric',
                                        hour12: true
                                    });
                                    document.getElementById('taskStart').textContent = startTime;
                                    document.getElementById('taskEnd').textContent = dueTime;
                                    document.getElementById('taskAssignedTo').textContent = data.assigned_to;
                                    const taskStatusElement = document.getElementById('taskStatus');
                                    switch (data.status.toLowerCase()) {
                                        case 'completed':
                                            taskStatusElement.className = 'green-text text';
                                            taskStatusElement.textContent = 'Completed';
                                            break;
                                        case 'pending':
                                            taskStatusElement.className = 'orange-text text';
                                            taskStatusElement.textContent = 'Pending';
                                            break;
                                        case 'missed':
                                            taskStatusElement.className = 'red-text text';
                                            taskStatusElement.textContent = 'Missed';
                                            break;
                                        case 'in-progress':
                                            taskStatusElement.className = 'blue-text text';
                                            taskStatusElement.textContent = 'In-Progress';
                                            break;
                                        default:
                                            taskStatusElement.className = 'gray-text text';
                                            taskStatusElement.textContent = data.status;
                                    }
                                    const taskFileElement = document.getElementById('taskFile');
                                    if (data.attachment) {
                                        taskFileElement.innerHTML = `<a href="${data.attachment}" target="_blank" class="blue-text text"><i class="fa-solid fa-paperclip"></i> Attachment</a>`;
                                    } else {
                                        taskFileElement.innerHTML = '<span class="gray-text text"><i class="fa-solid fa-xmark"></i> No Attachment</span>';
                                    }
                                    const proofFileElement = document.getElementById('proofFile');
                                    if (data.proof) {
                                        proofFileElement.innerHTML = `<a href="${data.proof}" target="_blank" class="blue-text text"><i class="fa-solid fa-paperclip"></i> Proof</a>`;
                                    } else {
                                        proofFileElement.innerHTML = '<span class="gray-text text"><i class="fa-solid fa-xmark"></i> No Proof</span>';
                                    }
                                    console.log('Comments:', data.comments); // Log the comments for debugging
                                    document.getElementById('taskComments').textContent = data.comments ? data.comments : 'None';
                                    document.getElementById('detailsModal').style.display = 'flex';
                                }
                            } catch (error) {
                                console.error('Error parsing JSON:', error);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });

            // RESTORE TASK
            document.querySelectorAll('.restoreTaskButton').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    const buttonElement = this;

                    Swal.fire({
                        title: 'Restore Task?',
                        text: 'Are you sure you want to restore this task?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('db/db_restore-task.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'task_id=' + taskId
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: 'Task Restored',
                                            text: 'The task has been restored',
                                            icon: 'success'
                                        }).then(() => {
                                            buttonElement.closest('tr').remove();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'An error occurred while restoring the task',
                                            icon: 'error'
                                        });
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                        }
                    });
                });
            });

            // DELETE TASK
            document.querySelectorAll('.deleteTaskButton').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    const buttonElement = this;

                    Swal.fire({
                        title: 'Delete Task?',
                        text: 'Are you sure you want to delete this task?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('db/db_delete-task.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'task_id=' + taskId
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: 'Task Deleted',
                                            text: 'The task has been deleted',
                                            icon: 'success'
                                        }).then(() => {
                                            buttonElement.closest('tr').remove();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'An error occurred while deleting the task',
                                            icon: 'error'
                                        });
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                        }
                    });
                });
            });
        });

        // CLOSE MODAL THAT IS CURRENTLY OPENED
        function closeModalDetails(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>

</html>