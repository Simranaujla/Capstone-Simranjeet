<?php 
 
 require_once '../config/session_check.php';
 require_once '../config/database.php';

 // Verify user login session
 if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
 }

 // Only employees can access the time tracking page
if ($_SESSION['role_id'] != 2) {
    header("Location: dashboard.php");
    exit();
}

 $message = "";

 if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $user_id = $_SESSION['user_id'];

    $action = $_POST['action'];

    $work_date = date("Y-m-d");



    //Punch out 

    if ($action == "punch_out") {

    $find_sql = "SELECT * FROM time_records
     WHERE user_id = ? 
     AND punch_out is NULL 
     ORDER BY record_id DESC
     LIMIT 1 ";
    

    $find_stmt = mysqli_prepare($conn, $find_sql);

    mysqli_stmt_bind_param(
        $find_stmt,
        "i",
        $user_id
    );
     
    mysqli_stmt_execute($find_stmt);

    $record = mysqli_fetch_assoc(
        mysqli_stmt_get_result($find_stmt)
    );

    if ($record){
        $punch_out = date("Y-m-d H:i:s");
        $hours=
        (
            strtotime($punch_out)
            -
            strtotime($record['punch_in'])
        ) / 3600;

        $update_sql = "UPDATE time_records
        SET punch_out = ? ,
        total_hours = ? 
        WHERE record_id = ? ";


        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param(
            $update_stmt,
            "sdi",
            $punch_out,
            $hours,
            $record['record_id']
        );

        mysqli_stmt_execute($update_stmt);

        $message = "Punch Out successful.";

    }
     else{
        $message = "No active punch in found.";
       
    }
    } elseif ($action == "punch_in"){
    // Save punch in timestamp
    $punch_in = date ("Y-m-d H:i:s");

    //Check if user already punched in today 
    $check_sql = "SELECT * FROM time_records
    WHERE user_id = ? 
    AND work_date = ? 
    AND punch_out IS NULL";

    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param(
        $check_stmt,
        "is",
        $user_id,
        $work_date

    );

    mysqli_stmt_execute($check_stmt);

    $check_result = mysqli_stmt_get_result($check_stmt);




    
    if (mysqli_num_rows($check_result) > 0){
        $message = "You already punched in today.";
    }else{
        // Insert time record into database
        $sql = "INSERT INTO time_Records
            (user_id, work_date, punch_in) 
            VALUES (?,?,?)";

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt){
             die(mysqli_error($conn));
        }

        mysqli_stmt_bind_param(
             $stmt,
             "iss",
             $user_id,
             $work_date,
             $punch_in
        );

        if (mysqli_stmt_execute($stmt)){
             $message = "Punch In successful.";
        }else{
             $message = mysqli_error($conn);
        }

     }
    
    }
    
    
 }

 // Fetch employee time records
 $records_sql = "SELECT * FROM time_records
 WHERE user_id = ? 
 ORDER BY record_id DESC ";


$records_stmt = mysqli_prepare($conn, $records_sql);

mysqli_stmt_bind_param(
    $records_stmt,
    "i",
    $_SESSION['user_id']
);

mysqli_stmt_execute($records_stmt);

$records_result = mysqli_stmt_get_result($records_stmt);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Time Tracking</title>
    </head>

    <body>
        <h1>Time Tracking</h1>

        <p> Welcome, <?php echo $_SESSION['full_name']; ?> </p>

        <p><?php echo $message; ?> </p>
        <form method="POST">

        <button type="submit" name ="action" value="punch_in">Punch In</button>
        <button type="submit" name ="action" value="punch_out">Punch Out</button>

        </form>

        <h2>Work Records</h2>
        <table border="1" cellpadding="10 ">
            <tr>
                <th>Date</th>
                <th>Punch In</th>
                <th>Punch Out</th>
                <th>Total Hours</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($records_result)) { ?>
             
              <tr>
                  <td><?php echo $row['work_date']; ?></td>
                  <td><?php echo $row['punch_in']; ?></td>
                  <td><?php echo $row['punch_out']; ?></td>
                  <td><?php echo $row['total_hours']; ?></td>
              </tr>
           
            <?php } ?>
        </table>

        <a href="dashboard.php">Back to Dashboard</a>
    </body>
</html>