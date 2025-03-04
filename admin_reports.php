<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <!-- STYLES -->
    <link rel="stylesheet" href="assets/styles/root.css">
    <link rel="stylesheet" href="assets/styles/admin_reports.css">
    <link rel="stylesheet" href="assets/styles/sidebar.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SWEETALERT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- COMPONENTS & PHP -->
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    include 'components/admin_sidebar.php';
    include 'db/db_reports.php';
    ?>

    <div class="main-content">
        <h1>Reports</h1>
        <p>Oversee each intern's task. You may assign a task to an intern, or to all of them.</p>
        <div class="header-container">
            <div class="search-section">
                <div class="search-bar">
                    <i class="fa fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search name...">
                </div>
                <button class="search-btn" id="searchBtn">Search</button>
                <button class="refresh-btn"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="button-group">
                <div class="status-dropdown-wrapper">
                    <select class="status-dropdown" id="statusFilter">
                        <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Select Status</option>
                        <option value="in-progress" <?= $status_filter === 'in-progress' ? 'selected' : '' ?>>In-Progress
                        </option>
                        <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed
                        </option>
                        <option value="missed" <?= $status_filter === 'missed' ? 'selected' : '' ?>>Missed</option>
                        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
                <button class="assign-task-btn" id="assignTaskBtn">
                    <i class="fa fa-plus"></i> Assign Task
                </button>
            </div>
        </div>
        <div class="table-container">
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
                <tbody id="taskTableBody">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($task = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['taskname']); ?></td>
                                <td><?= htmlspecialchars($task['assigned_to']); ?></td>
                                <td>
                                    <?php
                                    $startTime = new DateTime($task['starttime']);
                                    echo $startTime->format('F j, Y g:i A');
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $dueTime = new DateTime($task['duetime']);
                                    echo $dueTime->format('F j, Y g:i A');
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    switch (strtolower($task['status'])) {
                                        case 'completed':
                                            echo '<span class="green-text text">Completed</span>';
                                            break;
                                        case 'pending':
                                            echo '<span class="orange-text text">Pending</span>';
                                            break;
                                        case 'missed':
                                            echo '<span class="red-text text">Missed</span>';
                                            break;
                                        case 'in-progress':
                                            echo '<span class="blue-text text">In-Progress</span>';
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
                                    <button class="action-btn viewTaskBtn" data-id="<?= $task['id'] ?>">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <?php if (strtolower($task['status']) !== 'completed'): ?>
                                        <button class="action-btn editTaskBtn" data-id="<?= $task['id'] ?>">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="action-btn deleteTaskBtn" data-id="<?= $task['id'] ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <?php if (!empty($task['proof']) && strtolower($task['status']) !== 'completed'): ?>
                                        <button class="action-btn markCompleteBtn" data-id="<?= $task['id'] ?>">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <h2><i class="fa-solid fa-folder-open"></i> No data found</h2>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination Controls -->
        <div class="pagination">
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= $search_filter ?>"
                            class="<?= ($i == $currentPage) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Assign Task Modal -->
        <div id="assignTaskModal" class="modal-overlay">
            <div class="modal-container">
                <h2>Create New Task</h2>
                <!-- Form -->
                <form id="taskForm" enctype="multipart/form-data">
                    <label for="taskName">Task Name</label>
                    <input type="text" id="taskName" name="taskName" required>
                    <label for="taskDescription">Task Description</label>
                    <textarea id="taskDescription" name="taskDescription" required></textarea>
                    <!-- Dropbox to assign who to assign the task to -->
                    <label for="assignTo">Assign To</label>
                    <select id="assignto" name="assignTo" required>
                        <option value="">Select Intern</option>
                        <?php
                        include 'db/db_connection.php';
                        // Query to get all interns' ids and fullnames
                        $internQuery = "SELECT id, fullname FROM users WHERE position = 'Intern'";
                        $internResult = $conn->query($internQuery);
                        if ($internResult && $internResult->num_rows > 0) {
                            while ($intern = $internResult->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($intern['id']) . '">' . htmlspecialchars($intern['fullname']) . '</option>';
                            }
                        }
                        ?>
                        <!-- More options -->
                    </select>
                    <!-- Time -->
                    <label for="startTime">Start Time</label>
                    <input type="datetime-local" id="startTime" name="startTime" required>
                    <label for="dueTime">End Time</label>
                    <input type="datetime-local" id="dueTime" name="dueTime" required>
                    <label for="attachment">Attachment</label>
                    <input type="file" id="attachment" name="attachment"
                        accept="image/*,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                    <!-- Buttons -->
                    <div class="modal-buttons">
                        <button type="submit" id="assignBtn" class="assign-btn">Assign</button>
                        <button onclick="closeModalDetails('assignTaskModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Task Details Modal -->
        <div id="detailsModal" class="modal-overlay">
            <div class="modal-container">
                <h1>Task Details</h1>
                <div class="task-info">
                    <p><strong>Title:</strong> <span id="taskTitle"></span></p>
                    <p><strong>Description:</strong> <span id="task_Description"></span></p>
                    <p><strong>Start:</strong> <span id="taskStart"></span></p>
                    <p><strong>End:</strong> <span id="taskEnd"></span></p>
                    <p><strong>Assigned To:</strong> <span id="taskAssignedTo"></span></p>
                    <p><strong>Status:</strong> <span id="taskStatus"></span></p>
                    <p><strong>Task Image / File:</strong> <span id="taskFile"></span></p>
                    <p><strong>Proof Image / File:</strong> <span id="proofFile"></span></p>
                </div>
                <div class="button-container">
                    <button onclick="closeModalDetails('detailsModal')">Close</button>
                </div>
            </div>
        </div>

        <!-- Edit Task Modal -->
        <div id="editTaskModal" class="modal-overlay">
            <div class="modal-container">
                <h2>Edit Task</h2>
                <!-- Form -->
                <form id="editTaskForm" enctype="multipart/form-data">
                    <input type="hidden" id="editTaskId" name="taskId">
                    <label for="editTaskName">Task Name</label>
                    <input type="text" id="editTaskName" name="taskName" required>
                    <label for="editTaskDescription">Task Description</label>
                    <textarea id="editTaskDescription" name="taskDescription" required></textarea>
                    <!-- Dropbox to assign who to assign the task to -->
                    <label for="editAssignTo">Assign To</label>
                    <select id="editAssignTo" name="assignTo" required>
                        <?php
                        include 'db/db_connection.php';
                        // Query to get all interns' ids and fullnames
                        $internQuery = "SELECT id, fullname FROM users WHERE position = 'Intern'";
                        $internResult = $conn->query($internQuery);
                        if ($internResult && $internResult->num_rows > 0) {
                            while ($intern = $internResult->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($intern['id']) . '">' . htmlspecialchars($intern['fullname']) . '</option>';
                            }
                        }
                        ?>
                        <!-- More options -->
                    </select>
                    <!-- Time -->
                    <label for="editStartTime">Start Time</label>
                    <input type="datetime-local" id="editStartTime" name="startTime">
                    <label for="editDueTime">End Time</label>
                    <input type="datetime-local" id="editDueTime" name="dueTime">
                    <label for="editAttachment">Attachment</label>
                    <input type="file" id="editAttachment" name="attachment"
                        accept="image/*,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                    <!-- Buttons -->
                    <div class="modal-buttons">
                        <button type="submit" id="editBtn" class="assign-btn">Update</button>
                        <button type="button" onclick="closeModalDetails('editTaskModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Handle search button click
            const searchBtn = document.getElementById('searchBtn');
            if (searchBtn) {
                searchBtn.addEventListener('click', function () {
                    const searchInput = document.getElementById('searchInput').value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('search', searchInput);
                    if (statusFilter) {
                        const selectedStatus = statusFilter.value;
                        currentUrl.searchParams.set('status', selectedStatus);
                    }
                    currentUrl.searchParams.set('page', 1); // Reset to the first page
                    window.location.href = currentUrl.toString();
                });
            }

            // Handle status filter change
            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.addEventListener('change', function () {
                    const selectedStatus = this.value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('status', selectedStatus);
                    currentUrl.searchParams.set('page', 1); // Reset to the first page
                    window.location.href = currentUrl.toString();
                });
            }

            // Handle refresh button click
            const refreshBtn = document.querySelector('.refresh-btn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function () {
                    window.location.href = 'admin_reports.php';
                });
            }
            
            // Assign a task to an intern
            document.getElementById('assignTaskBtn').addEventListener('click', function () {
                document.getElementById('assignTaskModal').style.display = 'flex';
            });

            // Handle form submission
            document.getElementById('taskForm').addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);

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

            // Check the task's details
            document.querySelectorAll('.viewTaskBtn').forEach(button => {
                button.addEventListener('click', function () {
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

                                    // Convert start and end times to Month Name, Date, Year Hour:Minute AM/PM format
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
                                    // Set the status text and class
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
                                    // Set the task file
                                    const taskFileElement = document.getElementById('taskFile');
                                    if (data.attachment) {
                                        taskFileElement.innerHTML = `<a href="${data.attachment}" target="_blank" class="blue-text text"><i class="fa-solid fa-paperclip"></i> Attachment</a>`;
                                    } else {
                                        taskFileElement.innerHTML = '<span class="gray-text text"><i class="fa-solid fa-xmark"></i> No Attachment</span>';
                                    }
                                    // Set the proof file
                                    const proofFileElement = document.getElementById('proofFile');
                                    if (data.proof) {
                                        proofFileElement.innerHTML = `<a href="${data.proof}" target="_blank" class="blue-text text"><i class="fa-solid fa-paperclip"></i> Proof</a>`;
                                    } else {
                                        proofFileElement.innerHTML = '<span class="gray-text text"><i class="fa-solid fa-xmark"></i> No Proof</span>';
                                    }
                                    document.getElementById('detailsModal').style.display = 'flex';
                                }
                            } catch (error) {
                                console.error('Error parsing JSON:', error);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });

            // Mark task as completed
            document.querySelectorAll('.markCompleteBtn').forEach(button => {
                button.addEventListener('click', function () {
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
                                            // Remove the task row from the table
                                            buttonElement.closest('tr').remove();
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

            // Edit task
            document.querySelectorAll('.editTaskBtn').forEach(button => {
                button.addEventListener('click', function () {
                    let taskId = this.getAttribute('data-id');

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
                                alert('Task not found');
                            } else {
                                document.getElementById('editTaskId').value = taskId;
                                document.getElementById('editTaskName').value = data.task_name;
                                document.getElementById('editTaskDescription').value = data.task_description;
                                document.getElementById('editAssignTo').value = data.assigned_to;
                                document.getElementById('editStartTime').value = data.start_time;
                                document.getElementById('editDueTime').value = data.due_time;
                                document.getElementById('editTaskModal').style.display = 'flex';
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });

            // Handle form submission for editing a task
            document.getElementById('editTaskForm').addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);

                // Log form data for debugging
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }

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

            // Handle delete of task
            document.querySelectorAll('.deleteTaskBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const taskId = this.getAttribute('data-id');
                    const buttonElement = this;

                    Swal.fire({
                        title: 'Delete Task?',
                        text: 'Are you sure you want to delete this task?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('db/db_delete-task.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: `task_id=${taskId}`
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Deleted!', 'Task has been deleted.', 'success')
                                            .then(() => {
                                                // Remove the task row from the table
                                                buttonElement.closest('tr').remove();
                                                location.reload();
                                            });
                                    } else {
                                        Swal.fire('Error!', 'Failed to delete task.', 'error');
                                    }
                                })
                                .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
                        }
                    });
                });
            });
        });

        // Close the task details modal
        function closeModalDetails(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>

</html>