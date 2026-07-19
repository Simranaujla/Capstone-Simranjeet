<?php
session_start();
require_once '../config/database.php';

//Restrict access to Employer accounts

if (!isset($_SESSION['user_id'])  || $_SESSION['role_id'] != 1){
    header("Location: dashboard.php");
    exit();
}


//Fetch employee profile information
$sql = "SELECT
user_id,
full_name,
email,
phone,
account_status
FROM users
WHERE role_id = 2
ORDER BY full_name";

$result = mysqli_query($conn,$sql);



?>


<!DOCTYPE html>
<html>
    <head>
        <title>Manage Employees</title>
    </head>

    <body>
        <h1>Manage Employees</h1>
        <table border = "1" cellpadding = "8">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

      
        
        <?php

        //Display employee profiles
        while ($row = mysqli_fetch_assoc($result)){
            ?>
            <tr>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['account_status']; ?></td>
                <td>
                    <a href = "edit_employee.php?id=<?php echo $row['user_id']; ?>">Edit</a>

                    <a href = "update_employee_status.php?id=<?php echo $row['user_id'];  ?>&status="Inactive"> Deactivate </a>
                </td>
            </tr>

        <?php
        }

        ?>

        </table>

        <br>
        <a href = "dashboard.php"> Back to Dashboard </a>


     
     </body>


</html>