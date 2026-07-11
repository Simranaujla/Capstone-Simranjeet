<?php
 session_start();
 require_once '../config/database.php';
 //Verify user login session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

//Restrict access to Employer only 
if ($_SESSION['role_id'] != 1){
    header("Location: dashboard.php");
    exit();
}

//Fetch all employee work records
$sql = "SELECT 
time_records.record_id,
users.full_name,
time_records.work_date,
time_records.punch_in,
time_records.punch_out,
time_records.total_hours,
time_records.approval_status
FROM time_records
INNER JOIN users
ON time_records.user_id = users.user_id
ORDER BY time_records.record_id DESC ";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Manage Records</title>
    </head>

    <body>
        <h1>Employee Work Records</h1>

        <table border="1" cellpadding="10">
            <tr>
                <th>Employee Name</th>
                <th>Date</th>
                <th>Punch In</th>
                <th>Punch Out</th>
                <th>Total Hours</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
              
               <tr>
                   <td><?php echo $row['full_name']; ?></td>
                   <td><?php echo $row['work_date']; ?></td>
                   <td><?php echo $row['punch_in']; ?></td>
                   <td><?php echo $row['punch_out']; ?></td>
                   <td><?php echo $row['total_hours']; ?></td>
                   <td><?php echo $row['approval_status']; ?></td>
                   <td>
                      
                      <a href="update_status.php?id=<?php echo $row['record_id']; ?>&status=Approved" onclick = "return confirm('Approve this work record?')" >Approve</a>
                      <a href ="update_status.php?id=<?php echo $row['record_id']; ?>&status=Rejected" onclick = "return conform('Reject this work record ?')" >Reject </a>
                   </td> 

                   
               </tr>
            <?php } ?>
        </table>

        <a href="dashboard.php">Back to Dashboard</a>
    </body>
</html>