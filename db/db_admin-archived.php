<?php
include 'db_connection.php';

$search_filter = $_GET['search'] ?? '';
$status_filter = 'archived';
$currentPage = $_GET['page'] ?? 1;
$itemsPerPage = 10;
$offset = ($currentPage - 1) * $itemsPerPage;

// Fetch archived tasks
$sql = "SELECT * FROM tasks WHERE status = 'archived' AND (taskname LIKE ? OR taskdescription LIKE ?) LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $search_filter . '%';
$stmt->bind_param("ssii", $searchTerm, $searchTerm, $offset, $itemsPerPage);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of archived tasks for pagination
$sqlTotal = "SELECT COUNT(*) as total FROM tasks WHERE status = 'archived' AND (taskname LIKE ? OR taskdescription LIKE ?)";
$stmtTotal = $conn->prepare($sqlTotal);
$stmtTotal->bind_param("ss", $searchTerm, $searchTerm);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result();
$totalTasks = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalTasks / $itemsPerPage);

$stmt->close();
$stmtTotal->close();
$conn->close();
?>