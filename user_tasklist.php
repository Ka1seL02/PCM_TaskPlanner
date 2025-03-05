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
    <link rel="stylesheet" href="assets/styles/user_tasklist.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    include 'components/user_sidebar.php';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
    ?>
    <div class="main-content">
        <h1>My Tasks</h1>
        <p>"Stay organized and on track—here are your tasks for today!"</p>
        <div class="status-bar">
            <div class="status-dropdown-wrapper">
                <select class="status-dropdown" id="statusFilter">
                    <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>Select Status</option>
                    <option value="in-progress" <?= $status_filter === 'in-progress' ? 'selected' : '' ?>>In-Progress</option>
                    <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="missed" <?= $status_filter === 'missed' ? 'selected' : '' ?>>Missed</option>
                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                </select>
            </div>
        </div>
        <div class="table-container">
            <!-- SETTING WHAT TO DISPLAY BASED ON STATUS FILTER -->
            <?php
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;
            // BASE QUERY WHEN USER LOGGED-IN
            $query = "SELECT * FROM tasks WHERE assignedto = ?";
            $params = [$id];
            // IF STATUS FILTER IS APPLIED
            if ($status_filter !== 'all') {
                $query .= " AND LOWER(status) = ?";
                $params[] = strtolower($status_filter);
            }
            $query .= " ORDER BY starttime DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            // SQL STATEMENTS
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Query preparation failed: " . $conn->error);
            }
            // BIND PARAMETERS TO SQL STATEMENT
            if ($status_filter !== 'all') {
                $stmt->bind_param("ssii", $id, $params[1], $limit, $offset);
            } else {
                $stmt->bind_param("sii", $id, $limit, $offset);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $tasks = $result->fetch_all(MYSQLI_ASSOC);
            // COUNT TOTAL NUMBER OF TASKS FOR PAGENATION
            $count_query = "SELECT COUNT(*) FROM tasks WHERE assignedto = ?";
            if ($status_filter !== 'all') {
                $count_query .= " AND LOWER(status) = ?";
            }
            $count_stmt = $conn->prepare($count_query);
            if ($status_filter !== 'all') {
                @$count_stmt->bind_param("ss", $id, strtolower($status_filter));
            } else {
                @$count_stmt->bind_param("s", $id);
            }
            $count_stmt->execute();
            $count_stmt->bind_result($total_tasks);
            $count_stmt->fetch();
            $count_stmt->close();
            // CALCULATE HOW MANY PAGES NEEDED FOR PAGENATION
            $total_pages = ceil($total_tasks / $limit);
            ?>
            <!-- TABLE DISPLAYING ALL TASKS -->
            <table>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Given</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th></th>
                        <th>Proof</th>
                        <th>Attachment</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['taskname']) ?></td>
                            <td>
                                <?php
                                $startTime = new DateTime($row['starttime']);
                                echo $startTime->format('F j, Y g:i A');
                                ?>
                            </td>
                            <td>
                                <?php
                                $dueTime = new DateTime($row['duetime']);
                                echo $dueTime->format('F j, Y g:i A');
                                ?>
                            </td>
                            <td>
                                <?php
                                switch (strtolower($row['status'])) {
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
                                        echo '<span class="gray-text text">' . htmlspecialchars($row['status']) . '</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (strtolower($row['status']) === 'pending'): ?>
                                    <button class="accept-btn acceptTaskBtn" data-id="<?= $row['id'] ?>">
                                        <i class="fa fa-check"></i> Accept
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= !empty($row['proof'])
                                    ? '<a href="' . htmlspecialchars($row['proof'], ENT_QUOTES, 'UTF-8') . '" target="_blank" class="blue-text text"><i class="fa-solid fa-paperclip"></i> Proof</a>'
                                    : '<span class="gray-text text"><i class="fa-solid fa-xmark"></i> No Proof</span>';
                                ?>
                            </td>
                            <td>
                                <?= !empty($row['attachment'])
                                    ? '<a href="' . htmlspecialchars($row['attachment'], ENT_QUOTES, 'UTF-8') . '" target="_blank" class="blue-text text"><i class="fa-solid fa-paperclip"></i> Attachment</a>'
                                    : '<span class="gray-text text"><i class="fa-solid fa-xmark"></i> No Attachment</span>';
                                ?>
                            </td>
                            <td>
                                <button class="action-btn viewTaskBtn" data-id="<?= $row['id'] ?>">
                                    <i class="fa fa-eye"></i>   
                                </button>
                                <?php if (strtolower($row['status']) !== 'completed' &&  strtolower($row['status']) !== 'pending'): ?>
                                    <button class="action-btn submitTaskBtn" data-id="<?= $row['id'] ?>">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- PAGENATION -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>&status=<?= htmlspecialchars($status_filter) ?>"
                        class="<?= ($page == $i) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
    <!-- TASK DETAILS MODAL -->
    <div id="detailsModal" class="modal-overlay">
        <div class="modal-container">
            <h1>Task Details</h1>
            <div class="task-info">
                <p><strong>Title:</strong> <span id="taskTitle"></span></p>
                <p><strong>Description:</strong> <span id="taskDescription"></span></p>
                <p><strong>Start:</strong> <span id="taskStart"></span></p>
                <p><strong>End:</strong> <span id="taskEnd"></span></p>
                <p><strong>Assigned To:</strong> <span id="taskAssignedTo"></span></p>
                <p><strong>Status:</strong> <span id="taskStatus"></span></p>
                <p><strong>Task Image / File:</strong> <span id="taskFile"></span></p>
                <p><strong>Proof Image / File:</strong> <span id="proofFile"></span></p>
            </div>
            <div class="modal-buttons">
                <button onclick="closeModalDetails('detailsModal')">Close</button>
            </div>
        </div>
    </div>
    <!-- SUBMIT PROOF MODAL -->
    <div id="submitProofModal" class="modal-overlay">
        <div class="modal-container">
            <h1>Submit Task</h1>
            <div class="task-info">
                <input type="hidden" id="taskID">
                <input type="file" id="proofSubmit" name="proofSubmit" accept="image/*,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
            </div>
            <div class="button-container">
                <button class="submitProofBtn">Submit</button>
                <button onclick="closeModalDetails('submitProofModal')">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // HANDLE FILTER CHANGE
            document.getElementById('statusFilter').addEventListener('change', function() {
                const selectedStatus = this.value;
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('status', selectedStatus);
                currentUrl.searchParams.set('page', 1);
                window.location.href = currentUrl.toString();
            });
            // USER ACCEPT TASK (CHANGE STATUS FROM PENDING -> IN-PROGRESS)
            document.querySelectorAll(".acceptTaskBtn").forEach(button => {
                button.addEventListener("click", function() {
                    const taskId = this.getAttribute("data-id");
                    const buttonElement = this;
                    Swal.fire({
                        title: "Accept Task?",
                        text: "Are you sure you want to mark this task as in-progress?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Accept",
                        cancelButtonText: "Cancel",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("db/db_accept-task.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: `taskId=${taskId}`
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        const statusElement = buttonElement.closest("tr").querySelector("td:nth-child(4)");
                                        statusElement.innerHTML = '<span class="blue-text text">In-Progress</span>';
                                        buttonElement.remove();
                                        Swal.fire("Success!", "Task is now In-Progress.", "success")
                                            .then(() => location.reload());
                                    } else {
                                        Swal.fire("Error!", "Failed to update task status.", "error");
                                    }
                                })
                                .catch(error => Swal.fire("Error!", "Something went wrong.", "error"));
                        }
                    });
                });
            });
            // FETCH AND DISPLAY TASK'S DETAILS
            document.querySelectorAll('.viewTaskBtn').forEach(button => {
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
                                    document.getElementById('taskDescription').textContent = data.task_description;

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
                                    // CUSTOM STATUS TEXT STYLE
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
                                    // CUSTOM ATTACHMENT & PROOF STYLE
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
                                    document.getElementById('detailsModal').style.display = 'flex';
                                }
                            } catch (error) {
                                console.error('Error parsing JSON:', error);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
            // DISPLAY PROOF MODAL
            document.querySelectorAll('.submitTaskBtn').forEach(button => {
                button.addEventListener('click', function() {
                    const taskId = this.getAttribute("data-id");
                    document.getElementById('taskID').textContent = taskId;
                    document.getElementById('submitProofModal').style.display = 'flex';
                })
            })
            // SUBMIT PROOF MODAL
            document.querySelector('.submitProofBtn').addEventListener('click', function() {
                const taskId = document.getElementById('taskID').textContent;
                const fileInput = document.getElementById('proofSubmit');
                const file = fileInput.files[0];
                // IF NO FILES IS SUBMITTED
                if (!file) {
                    alert("No file is uploaded");
                    return;
                }
                const formData = new FormData();
                formData.append('taskId', taskId);
                formData.append('proofSubmit', file);
                // UPLOAD PROOF FILE
                fetch('db/db_submit-proof.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Success!", "Proof has been submitted.", "success")
                                .then(() => location.reload());
                        } else {
                            Swal.fire("Error!", data.message || "Failed to submit proof.", "error");
                        }
                    })
                    .catch(error => Swal.fire("Error!", "Something went wrong.", "error"));
            });
        });
        // CLOSE CURRENT OPEN MODAL
        function closeModalDetails(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>

</html>