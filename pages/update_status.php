<?php
session_start();
require_once '../config/session_check.php';
require_once '../config/database.php';

//Restrict access to Employer only
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: dashboard.php");
    exit();
}

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage_records.php");
    exit();
}


// Validate CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';

if (
    !isset($_SESSION['csrf_token']) ||
    !is_string($csrf_token) ||
    !hash_equals($_SESSION['csrf_token'], $csrf_token)
) {
    header("Location: manage_records.php");
    exit();
}

// Get and validate record ID
$record_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($record_id === false || $record_id === null || $record_id <= 0) {
    header("Location: manage_records.php");
    exit();
}


// Get approval status
$status = $_POST['status'] ?? '';

// Validate approval status
if ($status != "Approved" && $status != "Rejected") {
    header("Location: manage_records.php");
    exit();
}

//Update approval status

$sql = " UPDATE time_records
SET approval_status = ?
WHERE record_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $status,
    $record_id
);


mysqli_stmt_execute($stmt);

//Redirect back to management page
header("Location: manage_records.php");

exit();

?>

