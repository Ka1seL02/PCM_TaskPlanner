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
    <title>Admin Intern</title>
    <!-- STYLES -->
    <link rel="stylesheet" href="assets/styles/root.css">
    <link rel="stylesheet" href="assets/styles/admin_intern-list.css">
    <link rel="stylesheet" href="assets/styles/sidebar.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php $current_page = basename($_SERVER['PHP_SELF']);
    include 'components/admin_sidebar.php';
    include 'db/db_connection.php';
    // FETCH USERS BASED IF ADMIN OR SUPERVISOR LOGGED IN
    $position = $_SESSION['position'];
    $department = $_SESSION['department'];
    $limit = 10; // ALLOWED RECORDS TO SHOW BEFORE MAKING PAGENATION
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    $department_filter = isset($_GET['department']) ? $_GET['department'] : 'all';
    $search_filter = isset($_GET['search']) ? $_GET['search'] : '';
    if ($position == 'Admin') {
        if ($department_filter === 'all') {
            $userQuery = "SELECT id, fullname, department FROM users WHERE position = 'Intern' AND fullname LIKE ? LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($userQuery);
            $search_param = '%' . $search_filter . '%';
            $stmt->bind_param('sii', $search_param, $limit, $offset);
        } else {
            $userQuery = "SELECT id, fullname, department FROM users WHERE position = 'Intern' AND department = ? AND fullname LIKE ? LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($userQuery);
            $search_param = '%' . $search_filter . '%';
            $stmt->bind_param('ssii', $department_filter, $search_param, $limit, $offset);
        }
    } else if ($position == 'Supervisor') {
        $userQuery = "SELECT id, fullname, department FROM users WHERE position = 'Intern' AND department = ? AND fullname LIKE ? LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($userQuery);
        $search_param = '%' . $search_filter . '%';
        $stmt->bind_param('ssii', $department, $search_param, $limit, $offset);
    }
    $stmt->execute();
    $userResult = $stmt->get_result();
    $users = [];
    while ($user = $userResult->fetch_assoc()) {
        // Fetch the number of tasks assigned to the user that are not completed
        $taskQuery = "SELECT COUNT(*) as task_count FROM tasks WHERE assignedto = ? AND status IN ('In-Progress', 'Pending', 'Missed')";
        $taskStmt = $conn->prepare($taskQuery);
        $taskStmt->bind_param('i', $user['id']);
        $taskStmt->execute();
        $taskResult = $taskStmt->get_result();
        $taskCount = $taskResult->fetch_assoc()['task_count'];
        $user['task_count'] = $taskCount;
        $users[] = $user;
    }
    // CALCULATE HOW MANY PAGES ARE NEEDED
    if ($position == 'Admin') {
        if ($department_filter === 'all') {
            $totalUsersQuery = "SELECT COUNT(*) as total FROM users WHERE position = 'Intern' AND fullname LIKE ?";
            $totalStmt = $conn->prepare($totalUsersQuery);
            $totalStmt->bind_param('s', $search_param);
        } else {
            $totalUsersQuery = "SELECT COUNT(*) as total FROM users WHERE position = 'Intern' AND department = ? AND fullname LIKE ?";
            $totalStmt = $conn->prepare($totalUsersQuery);
            $totalStmt->bind_param('ss', $department_filter, $search_param);
        }
    } else if ($position == 'Supervisor') {
        $totalUsersQuery = "SELECT COUNT(*) as total FROM users WHERE position = 'Intern' AND department = ? AND fullname LIKE ?";
        $totalStmt = $conn->prepare($totalUsersQuery);
        $totalStmt->bind_param('ss', $department, $search_param);
    }
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $totalUsers = $totalResult->fetch_assoc()['total'];
    $totalPages = ceil($totalUsers / $limit);
    ?>
    <!-- MAIN CONTENT -->
    <div class="main-content">
        <h1>Intern List</h1>
        <p>See if interns' have task at hands.</p>
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
                <!-- ONLY SHOW DROPDOWN IF USER IS ADMIN -->
                <?php if ($_SESSION['position'] == 'Admin'): ?>
                    <div class="button-group">
                        <div class="department-dropdown-wrapper">
                            <select class="department-dropdown" id="departmentFilter">
                                <option value="all" <?= $department_filter === 'all' ? 'selected' : '' ?>>All Departments
                                </option>
                                <option value="IT" <?= $department_filter === 'IT' ? 'selected' : '' ?>>IT</option>
                                <option value="HR" <?= $department_filter === 'HR' ? 'selected' : '' ?>>HR</option>
                                <option value="Marketing" <?= $department_filter === 'Marketing' ? 'selected' : '' ?>>Marketing
                                </option>
                                <option value="Admin" <?= $department_filter === 'Admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- TABLE TO DISPLAY USERS AND HOW MANY TASK THEY HAVE -->
        <div class="table-container">
            <?php if ($userResult->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Tasks</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['department'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= $user['task_count'] ?></td>
                                <td>
                                    <span class="<?= $user['task_count'] > 0 ? 'text green-text' : 'text red-text' ?>">
                                        <?= $user['task_count'] > 0 ? 'Task Assigned' : 'No tasks currently' ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="assignTaskBtn" data-id="<?= $user['id'] ?>">
                                        <i class="fa fa-clipboard"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h2 class="no-data"><i class="fa-solid fa-folder-open"></i> No data found</h2>
            <?php endif; ?>
        </div>
        <!-- PAGENATION-->
        <?php if ($totalUsers > $limit): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="prev">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="next">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <!-- ASSIGN TASK MODAL -->
        <div id="assignTaskModal" class="modal-overlay">
            <div class="modal-container">
                <h2>Create New Task</h2>
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
                    </select>
                    <label for="startTime">Start Time</label>
                    <input type="datetime-local" id="startTime" name="startTime" required>
                    <label for="dueTime">End Time</label>
                    <input type="datetime-local" id="dueTime" name="dueTime" required>
                    <label for="attachment">Attachment</label>
                    <input type="file" id="attachment" name="attachment"
                        accept="image/*,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                    <div class="modal-buttons">
                        <button type="submit" id="assignBtn" class="assign-btn">Assign</button>
                        <button onclick="closeModalDetails('assignTaskModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // DROPDOWN FILTER CHANGE
            const departmentFilter = document.getElementById('departmentFilter');
            if (departmentFilter) {
                departmentFilter.addEventListener('change', function () {
                    const selectedDepartment = this.value;
                    const searchInput = document.getElementById('searchInput').value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('department', selectedDepartment);
                    currentUrl.searchParams.set('search', searchInput);
                    currentUrl.searchParams.set('page', 1); // Reset to the first page
                    window.location.href = currentUrl.toString();
                });
            }
            // HANDLE SEARCH BUTTON CLICKED
            const searchBtn = document.getElementById('searchBtn');
            if (searchBtn) {
                searchBtn.addEventListener('click', function () {
                    const searchInput = document.getElementById('searchInput').value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('search', searchInput);
                    if (departmentFilter) {
                        const selectedDepartment = departmentFilter.value;
                        currentUrl.searchParams.set('department', selectedDepartment);
                    }
                    currentUrl.searchParams.set('page', 1); // Reset to the first page
                    window.location.href = currentUrl.toString();
                });
            }
            // HANDLE REFRESH BUTTON CLICKED
            const refreshBtn = document.querySelector('.refresh-btn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function () {
                    window.location.href = 'admin_intern-list.php';
                });
            }
            // HANDLE ASSIGN TASK BUTTON CLICKED
            document.querySelectorAll('.assignTaskBtn').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = this.getAttribute('data-id');
                    const assignTaskModal = document.getElementById('assignTaskModal');
                    const assignToSelect = document.getElementById('assignto');
                    assignToSelect.value = userId;
                    assignToSelect.disabled = true;
                    assignTaskModal.style.display = 'flex';
                });
            });
            // HANDLE FORM SUBMISSION TO ASSIGN TASK
            document.getElementById('taskForm').addEventListener('submit', function (event) {
                event.preventDefault();
                const assignToSelect = document.getElementById('assignto');
                assignToSelect.disabled = false;
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
        });
        // CLOSE ASSIGN TASK MODAL
        window.closeModalDetails = function (modalId) {
            document.getElementById(modalId).style.display = 'none';
        };
    </script>
</body>

</html>