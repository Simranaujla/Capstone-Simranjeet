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


//Get filter work records

$start_date = "";
$end_date = "";
if(isset($_GET['start_date'])){
    $start_date = $_GET['start_date'];
}


if(isset($_GET['end_date'])){
    $end_date = $_GET['end_date'];
}

//Get sorting option
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

//Set sorting based on selected option
if($sort == 'date_asc'){
    $order_by = "time_records.work_date ASC";
}
elseif($sort == 'hours_desc'){
    $order_by = "time_Records.total_hours DESC";
}
elseif($sort == 'hours_asc'){
    $order_by = "time_records.total_hours ASC";
}
else{
    $order_by = "time_records.work_date DESC";
}

//Fetch all employee work records
if(!empty($start_date) && !empty($end_date)){


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
    WHERE time_records.work_date BETWEEN ? AND ?
    ORDER BY $order_by ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $start_date,
        $end_date
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
else{
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
    ORDER BY $order_by";

    $result = mysqli_query($conn,$sql);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Manage Records</title>
    </head>

    <body>
        <h1>Employee Work Records</h1>

        <!-- Work records date filter --->
        <form method="GET">

            <label>From:</label>
            <input type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">

            <label>To:</label>
            <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">

            <button type="submit">Filter</button>


        </form>
        <br>

        <form method = "GET">

            <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">

            <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">


            <label>Sort by:</label>
            <select name="sort">
                <option value="date_desc">Date: Newest First</option>
                <option value="date_asc">Date: Oldest First</option>
                <option value="hours_desc">Hours: Highest First</option>
                <option value="hours_asc">Hours: Lowest First</option>
            </select>

            <button type="submit">Sort</button>

        </form>
        <br>

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
                      <a href ="update_status.php?id=<?php echo $row['record_id']; ?>&status=Rejected" onclick = "return confirm('Reject this work record ?')" >Reject </a>
                   </td> 

                   
               </tr>
            <?php } ?>
        </table>

        <a href="dashboard.php">Back to Dashboard</a>
    </body>
</html>