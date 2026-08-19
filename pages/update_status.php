<?php
session_start();
require_once '../config/session_check.php';
require_once '../config/database.php';

//Restrict access to Employer only
if ($_SESSION['role_id']!=1){
    header("Location: dashboard.php");
    exit();
}

$record_id = $_GET['id'];

$status = $_GET['status'];

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
header ("Location : manage_records.php");

exit();

?>

