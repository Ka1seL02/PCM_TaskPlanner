<?php
include 'db_connection.php';

// Determine the user's position and department
$position = $_SESSION['position']; // Assuming position is stored in session
$department = $_SESSION['department']; // Assuming department is stored in session

// Define the status filter and search filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_filter = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch tasks based on the user's position and department
$sql = "SELECT t.id, t.taskname, t.status, t.assignedto, t.starttime, t.duetime, t.proof, t.attachment, u.fullname AS assigned_to 
        FROM tasks t
        LEFT JOIN users u ON t.assignedto = u.id
        WHERE t.status !='Archived'";

// Add condition to filter tasks based on department if the user is a supervisor
if ($position === 'Supervisor') {
    $sql .= " AND u.department = '$department'";
}

// Add condition to filter tasks based on status
if ($status_filter !== 'all') {
    $sql .= " AND t.status = '$status_filter'";
}

// Add condition to filter tasks based on search input
if (!empty($search_filter)) {
    $sql .= " AND u.fullname LIKE '%$search_filter%'";
}

$sql .= " ORDER BY t.id";

// Pagination
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$sql .= " LIMIT $itemsPerPage OFFSET $offset";

$result = $conn->query($sql);

// Fetch total number of tasks for pagination
$totalTasksSql = "SELECT COUNT(*) AS total FROM tasks t LEFT JOIN users u ON t.assignedto = u.id WHERE t.status !='Archived'";
if ($position === 'Supervisor') {
    $totalTasksSql .= " AND u.department = '$department'";
}
if ($status_filter !== 'all') {
    $totalTasksSql .= " AND t.status = '$status_filter'";
}
if (!empty($search_filter)) {
    $totalTasksSql .= " AND u.fullname LIKE '%$search_filter%'";
}
$totalResult = $conn->query($totalTasksSql);
$totalTasks = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalTasks / $itemsPerPage);

$conn->close();
?>