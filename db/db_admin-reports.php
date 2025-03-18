<?php 
include 'db_connection.php';

// Determine the user's position and department
$position = $_SESSION['position'];
$department = $_SESSION['department'];

// Define the filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_filter = isset($_GET['search']) ? $_GET['search'] : '';
$school_filter = isset($_GET['school']) ? $_GET['school'] : 'all';

// Start building the SQL query
$sql = "SELECT t.*, u.fullname, u.school, u.pfp
        FROM tasks t
        LEFT JOIN users u ON t.assignedto = u.id
        WHERE t.status != 'Archived'";

// Add condition to filter tasks based on department if the user is a supervisor
if ($position === 'Supervisor') {
    $sql .= " AND u.department = '$department'";
}

// Add condition to filter tasks based on status
if ($status_filter !== 'all') {
    $sql .= " AND t.status = '$status_filter'";
}

// Add condition to filter tasks based on search term
if (!empty($search_filter)) {
    $sql .= " AND t.taskname LIKE '%$search_filter%'";
}

// Add condition to filter tasks based on school
if ($school_filter !== 'all') {
    $sql .= " AND u.school = '" . $conn->real_escape_string($school_filter) . "'";
}

$sql .= " ORDER BY t.id";

// Pagination
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Create a separate SQL query for counting total results (without LIMIT)
$countSql = $sql;
$sql .= " LIMIT $itemsPerPage OFFSET $offset";

$result = $conn->query($sql);

// Fetch total number of tasks for pagination
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM ($countSql) AS count_table");
$totalTasks = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalTasks / $itemsPerPage);

if ($result === false) {
    echo "Error: " . $conn->error;
    exit();
}
?>