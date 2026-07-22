<?php
session_start();
require_once '../config/database.php';

//Restrict access to Employer accounts
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location:dashboard.php");
    exit();
}


$message = "";
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Create Employee</title>
    </head>

    <body>
        <h1>Create Employee</h1>

        <?php if (!empty($message)) { ?>
            <p><?php echo $message; ?></p>
        <?php } ?>

        <form method="POST">

            <label>Full Name</label><br>
            <input type="text" name="full_name"><br><br>

            <label>Email</label><br>
            <input type="email" name="email"><br><br>

            <label>Phone</label><br>
            <input type="text" name="phone"><br><br>

            <label>Password</label><br>
            <input type="password" name="password"><br><br>

        </form>
        <br>

        <a href="manage_employees.php">Back to Manage Employees </a>

        
    </body>
</html>