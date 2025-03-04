<?php
include 'db_connection.php';

// Fetch department-wise intern count
$query = "SELECT department, COUNT(*) AS intern_count FROM users WHERE position = 'Intern' GROUP BY department";
$result = $conn->query($query);

$internCounts = ['Marketing' => 0, 'Admin' => 0, 'IT' => 0, 'HR' => 0];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (array_key_exists($row['department'], $internCounts)) {
            $internCounts[$row['department']] = $row['intern_count'];
        }
    }
}

// Determine the user's position and department
$position = $_SESSION['position']; // Assuming position is stored in session
$department = $_SESSION['department']; // Assuming department is stored in session

// Fetch tasks grouped by status
$sql = "SELECT t.id, t.taskname, t.status, u.fullname AS assigned_to, u.department 
        FROM tasks t
        LEFT JOIN users u ON t.assignedto = u.id";

// Add condition to filter tasks based on department if the user is a supervisor
if ($position === 'Supervisor') {
    $sql .= " WHERE u.department = '$department'";
}

$sql .= " ORDER BY t.status, t.id";

$result = $conn->query($sql);

// Store tasks in separate arrays
$tasks = [
    'In-Progress' => [],
    'Completed' => [],
    'Pending' => [],
    'Missed' => []
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[$row['status']][] = $row;
    }
}

// SET FOR MAXIMUM ENTRIES TO SHOW
$itemsPerPage = 5;  // Show only 5 tasks per page

// Get current page for each status
$currentPageInProgress = isset($_GET['page_in_progress']) ? (int)$_GET['page_in_progress'] : 1;
$currentPageCompleted = isset($_GET['page_completed']) ? (int)$_GET['page_completed'] : 1;
$currentPagePending = isset($_GET['page_pending']) ? (int)$_GET['page_pending'] : 1;
$currentPageMissed = isset($_GET['page_missed']) ? (int)$_GET['page_missed'] : 1;

// Calculate offsets for each status
$offsetInProgress = ($currentPageInProgress - 1) * $itemsPerPage;
$offsetCompleted = ($currentPageCompleted - 1) * $itemsPerPage;
$offsetPending = ($currentPagePending - 1) * $itemsPerPage;
$offsetMissed = ($currentPageMissed - 1) * $itemsPerPage;

// Get total pages for each task status
$totalTasksInProgress = count($tasks['In-Progress']);
$totalPagesInProgress = ceil($totalTasksInProgress / $itemsPerPage);

$totalTasksCompleted = count($tasks['Completed']);
$totalPagesCompleted = ceil($totalTasksCompleted / $itemsPerPage);

$totalTasksPending = count($tasks['Pending']);
$totalPagesPending = ceil($totalTasksPending / $itemsPerPage);

$totalTasksMissed = count($tasks['Missed']);
$totalPagesMissed = ceil($totalTasksMissed / $itemsPerPage);

// Slice the tasks array for the current page
$paginatedTasksInProgress = array_slice($tasks['In-Progress'], $offsetInProgress, $itemsPerPage);
$paginatedTasksCompleted = array_slice($tasks['Completed'], $offsetCompleted, $itemsPerPage);
$paginatedTasksPending = array_slice($tasks['Pending'], $offsetPending, $itemsPerPage);
$paginatedTasksMissed = array_slice($tasks['Missed'], $offsetMissed, $itemsPerPage);

$conn->close();
?>