<?php
session_start();
// CHECK IF LOGGED IN ELSE REDIRECT BACK TO LOGIN PAGE
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db/db_admin-reports.php'; // Include the logic for fetching tasks
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
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
        <h1>Reports</h1>
        <p>Oversee each intern's task. You may assign a task to an intern, or to all of them.</p>
        <!-- CONTROLS (SEARCH, REFRESH, SORT DROPDOWN & ADD TASK) -->
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
            <div class="right-section">
                <!-- DROPDOWN -->
                <div class="dropdown-wrapper">
                    <select id="statusDropdownFilter">
                        <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Select Status</option>
                        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in-progress" <?= $status_filter === 'in-progress' ? 'selected' : '' ?>>In-Progress
                        </option>
                        <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed
                        </option>
                        <option value="missed" <?= $status_filter === 'missed' ? 'selected' : '' ?>>Missed</option>
                    </select>
                </div>
                <!-- ASSIGN TASK BUTTON -->
                <button id="assignTaskButton">
                    <i class="fa fa-plus"></i> Assign Task
                </button>
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
                                <td><?= htmlspecialchars($task['assigned_to']); ?></td>
                                <td><?= date("F j, Y, g:i A", strtotime($task['starttime'])); ?></td>
                                <td><?= date("F j, Y, g:i A", strtotime($task['duetime'])); ?></td>
                                <td>
                                    <?php
                                    switch (strtolower($task['status'])) {
                                        case 'completed':
                                            echo '<span class="green-text text">' . htmlspecialchars($task["status"]) . '</span>';
                                            break;
                                        case 'pending':
                                            echo '<span class="orange-text text">' . htmlspecialchars($task["status"]) . '</span>';
                                            break;
                                        case 'missing':
                                        case 'late':
                                            echo '<span class="red-text text">' . htmlspecialchars($task["status"]) . '</span>';
                                            break;
                                        case 'in-progress':
                                        case 'revision':
                                        case 'revised':
                                            echo '<span class="blue-text text">' . htmlspecialchars($task["status"]) . '</span>';
                                            break;
                                        default:
                                            echo '<span class="gray-text text">' . htmlspecialchars($task['status']) . '</span>';
                                    }
                                    ?>
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
                                    <?php if (strtolower($task['status']) !== 'completed'): ?>
                                        <button class="action-btn editTaskButton" data-id="<?= $task['id'] ?>">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (strtolower($task['status']) === 'completed'): ?>
                                        <button class="action-btn archiveTaskButton" data-id="<?= $task['id'] ?>">
                                            <i class="fa fa-folder-open"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="action-btn deleteTaskButton" data-id="<?= $task['id'] ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <?php if (!empty($task['proof']) && strtolower($task['status']) !== 'completed'): ?>
                                        <button class="action-btn markCompleteButton" data-id="<?= $task['id'] ?>">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    <?php endif; ?>
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

        <!-- ASSIGN TASK MODAL -->
        <div id="assignTaskModal" class="modal-overlay">
            <div class="modal-container">
                <h2>Create New Task</h2>
                <form id="assignTaskForm" enctype="multipart/form-data">
                    <div class="main">
                        <div class="left-container">
                            <label for="taskName">Task Name</label>
                            <input type="text" id="taskName" name="taskName" required>
                            <label for="taskDescription">Task Description</label>
                            <textarea id="taskDescription" name="taskDescription" required></textarea>
                            <label for="assignTo">Assign To</label>
                            <select id="assignTo" name="assignTo" required>
                                <option>Select Intern</option>
                                <?php
                                include 'db/db_connection.php';
                                $internQuery = "SELECT id, fullname FROM users WHERE position = 'Intern' ORDER BY fullname ASC";
                                $internResult = $conn->query($internQuery);
                                if ($internResult && $internResult->num_rows > 0) {
                                    while ($intern = $internResult->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($intern['id']) . '">' . htmlspecialchars($intern['fullname']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="right-container">
                            <label for="dueTime">Due</label>
                            <div class="radio-buttons">
                                <div>
                                    <input type="radio" id="nextDay" name="dueTimeOption" value="nextDay" required>
                                    <label for="nextDay">Next Day</label>
                                </div>
                                <div>
                                    <input type="radio" id="nextWeek" name="dueTimeOption" value="nextWeek">
                                    <label for="nextWeek">Next Week</label>
                                </div>
                                <div>
                                    <input type="radio" id="custom" name="dueTimeOption" value="custom">
                                    <label for="custom">Custom</label>
                                </div>
                            </div>
                            <div id="customDateTimeContainer">
                                <label for="customDueTime">Custom Due Time</label>
                                <input type="datetime-local" id="customDueTime" name="customDueTime">
                            </div>
                            <label for="editAttachment">Attachment</label>
                            <input type="file" id="editAttachment" name="attachment"
                                accept="image/*,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                            <label for="taskComment">Comment</label>
                            <textarea id="taskComment" name="taskComment"></textarea>
                        </div>
                    </div>
                    <div class="modal-buttons">
                        <button type="submit" id="assignBtn" class="assign-btn">Assign</button>
                        <button onclick="closeModalDetails('assignTaskModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

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

        <!-- EDIT TASK MODAL -->
        <div id="editTaskModal" class="modal-overlay">
            <div class="modal-container">
                <h2>Edit Task</h2>
                <form id="editTaskForm" enctype="multipart/form-data">
                    <div class="main">
                        <div class="left-container">
                            <input type="hidden" id="editTaskId" name="taskId">
                            <label for="taskName">Task Name</label>
                            <input type="text" id="task_Name" name="taskName" required>
                            <label for="taskDescription">Task Description</label>
                            <textarea id="task_Description2" name="taskDescription" required></textarea>
                            <label for="assignTo">Assign To</label>
                            <select id="assign_To" name="assignTo" required>
                                <option>Select Intern</option>
                                <?php
                                include 'db/db_connection.php';
                                $internQuery = "SELECT id, fullname FROM users WHERE position = 'Intern' ORDER BY fullname ASC";
                                $internResult = $conn->query($internQuery);
                                if ($internResult && $internResult->num_rows > 0) {
                                    while ($intern = $internResult->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($intern['id']) . '">' . htmlspecialchars($intern['fullname']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="right-container">
                            <label for="dueTime">Due</label>
                            <input type="datetime-local" id="due_Time" name="dueTime">
                            <label for="editAttachment">Attachment</label>
                            <input type="file" id="edit_Attachment" name="attachment"
                                accept="image/*,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                            <label for="taskComment">Comment</label>
                            <textarea id="task_Comment" name="taskComment"></textarea>
                        </div>
                    </div>
                    <div class="modal-buttons">
                        <button type="submit" id="editTaskBtn">Update</button>
                        <button type="button" onclick="closeModalDetails('editTaskModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handle status dropdown change
            document.getElementById('statusDropdownFilter').addEventListener('change', function() {
                const selectedStatus = this.value;
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('status', selectedStatus);
                currentUrl.searchParams.set('page', 1); // Reset to the first page
                window.location.href = currentUrl.toString();
            });

            // Handle refresh button click
            document.getElementById('refreshButton').addEventListener('click', function() {
                window.location.href = 'admin_reports.php';
            });

            // Handle search button click
            document.getElementById('searchButton').addEventListener('click', function() {
                const searchInput = document.getElementById('searchInput').value;
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('search', searchInput);
                currentUrl.searchParams.set('page', 1); // Reset to the first page
                window.location.href = currentUrl.toString();
            });

            // SHOW ASSIGN TASK MODAL
            document.getElementById('assignTaskButton').addEventListener('click', function() {
                document.getElementById('assignTaskModal').style.display = 'flex';
            });

            // ASSIGN TASK MODAL DUE TIME
            document.querySelectorAll('input[name="dueTimeOption"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const customDateTimeContainer = document.getElementById('customDateTimeContainer');
                    if (this.value === 'custom') {
                        customDateTimeContainer.style.display = 'flex';
                    } else {
                        customDateTimeContainer.style.display = 'none';
                    }
                });
            });

            // ASSIGN TASK
            document.getElementById('assignTaskForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                // Calculate due time based on selected option
                const dueTimeOption = document.querySelector('input[name="dueTimeOption"]:checked').value;
                let dueTime;
                const now = new Date();

                if (dueTimeOption === 'nextDay') {
                    const nextDay = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 10, 0, 0); // Next day at 10 AM
                    dueTime = `${nextDay.getFullYear()}-${String(nextDay.getMonth() + 1).padStart(2, '0')}-${String(nextDay.getDate()).padStart(2, '0')}T10:00`;
                } else if (dueTimeOption === 'nextWeek') {
                    const nextWeek = new Date(now.getFullYear(), now.getMonth(), now.getDate() + (8 - now.getDay()), 10, 0, 0); // Next Monday at 10 AM
                    dueTime = `${nextWeek.getFullYear()}-${String(nextWeek.getMonth() + 1).padStart(2, '0')}-${String(nextWeek.getDate()).padStart(2, '0')}T10:00`;
                } else if (dueTimeOption === 'custom') {
                    dueTime = document.getElementById('customDueTime').value;
                }
                formData.append('dueTime', dueTime);
                fetch('db/db_assign-task.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', 'Task has been assigned.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error!', 'Failed to assign task.', 'error');
                        }
                    })
                    .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
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

            // MARK TASK AS COMPLETED
            document.querySelectorAll('.markCompleteButton').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    const buttonElement = this;

                    Swal.fire({
                        title: 'Mark Task as Completed?',
                        text: 'Are you sure you want to mark this task as completed?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('db/db_mark-complete.php', {
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
                                            title: 'Task Completed',
                                            text: 'The task has been marked as completed',
                                            icon: 'success'
                                        }).then(() => {
                                            buttonElement.closest('tr').remove();
                                            window.location.href = 'admin_reports.php';
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'An error occurred while marking the task as completed',
                                            icon: 'error'
                                        });
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                        }
                    })
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

            // ARCHIVE TASK
            document.querySelectorAll('.archiveTaskButton').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    const buttonElement = this;

                    Swal.fire({
                        title: 'Archive Task?',
                        text: 'Are you sure you want to archive this task?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('db/db_archive-task.php', {
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
                                            title: 'Task Archived',
                                            text: 'The task has been archived',
                                            icon: 'success'
                                        }).then(() => {
                                            buttonElement.closest('tr').remove();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'An error occurred while archiving the task',
                                            icon: 'error'
                                        });
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                        }
                    });
                });
            });

            // EDIT TASK BUTTON CLICKED
            document.querySelectorAll('.editTaskButton').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute('data-id');
                    fetch('db/db_view-task.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'task_id=' + taskId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                Swal.fire('Error!', 'Task not found.', 'error');
                            } else {
                                document.getElementById('editTaskId').value = data.id;
                                document.getElementById('task_Name').value = data.task_name;
                                document.getElementById('task_Description2').value = data.task_description;
                                document.getElementById('assign_To').value = data.assigned_to;

                                // Format the due_time to YYYY-MM-DDTHH:MM
                                const dueTime = new Date(data.due_time);
                                const formattedDueTime = dueTime.toISOString().slice(0, 16);
                                document.getElementById('due_Time').value = formattedDueTime;

                                document.getElementById('task_Comment').value = data.comments;
                                document.getElementById('editTaskModal').style.display = 'flex';
                            }
                        })
                        .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
                });
            });

            // HANDLE EDIT TASK FORM SUBMISSION
            document.getElementById('editTaskForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                fetch('db/db_edit-task.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', 'Task has been updated.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error!', 'Failed to update task.', 'error');
                        }
                    })
                    .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
            });

        });

        // CLOSE MODAL THAT IS CURRENTLY OPENED
        function closeModalDetails(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>

</html>