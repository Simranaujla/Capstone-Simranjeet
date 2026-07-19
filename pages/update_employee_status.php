<?php
session_start();
require_once '../config/database.php';

//Restrict access to Employer accounts
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: dashboard.php");
    exit();
}


//Get employee information
$user_id = $_GET['id'];
$status = $_GET['status'];

//Update employee account status
$sql = "UPDATE users
SET account_status = ?
WHERE iser_id =? ";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $status,
    $user_id,
);

mysqli_stmt_execute($stmt);

//Return to employee management page
header("Location: manage_employees.php");

exit();
?>