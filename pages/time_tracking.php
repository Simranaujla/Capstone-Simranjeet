<?php 
 
 require_once '../config/session_check.php';
 require_once '../config/database.php';

 // Verify user login session
 if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
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

//Calculate current week's total hours
$week_start = date("Y-m-d",strtotime("monday this week"));
$week_end = date("Y-m-d",strtotime("sunday this week"));

$weekly_sql = "SELECT SUM(total_hours) AS weekly_hours
               FROM time_records
               WHERE user_id = ?
               AND work_date BETWEEN ? AND ?";

$weekly_stmt = mysqli_prepare($conn,$weekly_sql);

mysqli_stmt_bind_param(
    $weekly_stmt,
    "iss",
    $_SESSION['user_id'],
    $week_start,
    $week_end
);

Based on what we've implemented so far:
✅ Daily work hours: Yes. Your time records display work_date, punch_in, punch_out, and total_hours.
❌ Weekly work hours: I don't think we've implemented a weekly total/summary yet.
So requirement 4.2 is only partially complete.
We should add a weekly view where an employee can see something like:

Week	Total Hours
Aug 17–23	38.5
Aug 10–16	42.0
I'd recommend doing Weekly Work Hours next, before CSS, because it's one of the remaining functional requirements.
lets do this 
Yes. Let's implement Weekly Work Hours now.
Since your requirement is:

4.2 Employees must be able to view their daily and weekly work hours.
Your existing time_tracking.php already handles the daily records, so we'll add a weekly total to the employee's view.
First step
Let's avoid creating a duplicate page unnecessarily.
Please paste the PHP section from your time_tracking.php (or whichever page employees currently use to see their work records), especially the part that:

SELECT ...
FROM time_records
and displays:
work_date
punch_in
punch_out
total_hours
I'll add the weekly calculation to your existing page so we don't disturb the functionality that's already working.
<?php
require_once '../config/session_check.php';

require_once '../config/database.php';

// Verify user login session

if (!isset($_SESSION['user_id'])){

header("Location: login.php");

exit();
}
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST"){

$user\_id = $\_SESSION['user\_id'];

$action = $\_POST['action'];

$work\_date = date("Y-m-d");





//Punch out 

if ($action == "punch\_out") {

$find\_sql = "SELECT \* FROM time\_records

 WHERE user\_id = ? 

 AND punch\_out is NULL 

 ORDER BY record\_id DESC

 LIMIT 1 ";

$find\_stmt = mysqli\_prepare($conn, $find\_sql);

mysqli\_stmt\_bind\_param(

    $find\_stmt,

    "i",

    $user\_id

);

mysqli\_stmt\_execute($find\_stmt);

$record = mysqli\_fetch\_assoc(

    mysqli\_stmt\_get\_result($find\_stmt)

);

if ($record){

    $punch\_out = date("Y-m-d H\:i:s");

    $hours=

    (

        strtotime($punch\_out)

        \-

        strtotime($record['punch\_in'])

    ) / 3600;

    $update\_sql = "UPDATE time\_records

    SET punch\_out = ? ,

    total\_hours = ? 

    WHERE record\_id = ? ";



    $update\_stmt = mysqli\_prepare($conn, $update\_sql);

    mysqli\_stmt\_bind\_param(

        $update\_stmt,

        "sdi",

        $punch\_out,

        $hours,

        $record['record\_id']

    );

    mysqli\_stmt\_execute($update\_stmt);

    $message = "Punch Out successful.";

}

 else{

    $message = "No active punch in found.";

}

} elseif ($action == "punch\_in"){

// Save punch in timestamp

$punch\_in = date ("Y-m-d H\:i:s");

//Check if user already punched in today 

$check\_sql = "SELECT \* FROM time\_records

WHERE user\_id = ? 

AND work\_date = ? 

AND punch\_out IS NULL";

$check\_stmt = mysqli\_prepare($conn, $check\_sql);

mysqli\_stmt\_bind\_param(

    $check\_stmt,

    "is",

    $user\_id,

    $work\_date

);

mysqli\_stmt\_execute($check\_stmt);

$check\_result = mysqli\_stmt\_get\_result($check\_stmt);







if (mysqli\_num\_rows($check\_result) > 0){

    $message = "You already punched in today.";

}else{

    // Insert time record into database

    $sql = "INSERT INTO time\_Records

        (user\_id, work\_date, punch\_in) 

        VALUES (?,?,?)";

    $stmt = mysqli\_prepare($conn, $sql);

    if (!$stmt){

         die(mysqli\_error($conn));

    }

    mysqli\_stmt\_bind\_param(

         $stmt,

         "iss",

         $user\_id,

         $work\_date,

         $punch\_in

    );

    if (mysqli\_stmt\_execute($stmt)){

         $message = "Punch In successful.";

    }else{

         $message = mysqli\_error($conn);

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

$records\_stmt,

"i",

$\_SESSION['user\_id']
);
mysqli_stmt_execute($records_stmt);

$records_result = mysqli_stmt_get_result($records_stmt);

?>

<!DOCTYPE html>

<html>

\<head>

    \<title>Time Tracking\</title>

\</head>

\<body>

    \<h1>Time Tracking\</h1>

    \<p> Welcome, \<?php echo $\_SESSION['full\_name']; ?> \</p>

    \<p>\<?php echo $message; ?> \</p>

    \<form method="POST">

    \<button type="submit" name ="action" value="punch\_in">Punch In\</button>

    \<button type="submit" name ="action" value="punch\_out">Punch Out\</button>

    \</form>

    \<h2>Work Records\</h2>

    \<table *border*="1" cellpadding="10 ">

        \<tr>

            \<th>Date\</th>

            \<th>Punch In\</th>

            \<th>Punch Out\</th>

            \<th>Total Hours\</th>

        \</tr>

        \<?php while($row = mysqli\_fetch\_assoc($records\_result)) { ?>

          \<tr>

              \<td>\<?php echo $row['work\_date']; ?>\</td>

              \<td>\<?php echo $row['punch\_in']; ?>\</td>

              \<td>\<?php echo $row['punch\_out']; ?>\</td>

              \<td>\<?php echo $row['total\_hours']; ?>\</td>

          \</tr>

        \<?php } ?>

    \</table>

    \<a href="dashboard.php">Back to Dashboard\</a>

\</body>
</html>
Perfect. Your time_tracking.php already shows daily hours. We can add a weekly total without changing your existing punch-in/punch-out logic.
Step 1 — Add weekly calculation
Place this code after your existing $records_result code and before ?>:
// Calculate current week's total hours
$week_start = date("Y-m-d", strtotime("monday this week"));
$week_end = date("Y-m-d", strtotime("sunday this week"));

$weekly_sql = "SELECT SUM(total_hours) AS weekly_hours
FROM time_records
WHERE user_id = ?
AND work_date BETWEEN ? AND ?";

$weekly_stmt = mysqli_prepare($conn, $weekly_sql);

mysqli_stmt_bind_param(
    $weekly_stmt,
    "iss",
    $_SESSION['user_id'],
    $week_start,
    $week_end
);

mysqli_stmt_execute($weekly_stmt);
$weekly_result = mysqli_stmt_get_result($weekly_stmt);
$weekly_data = mysqli_fetch_assoc($weekly_result);
$weekly_hours = $weekly_data['weekly_hours'] ?? 0;



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

        <h2>Weekly Hours</h2>

        <p>
            Week:
        <?php echo $week_start; ?>
            to
        <?php echo $week_end; ?>
        </p>

        <p>
            Total Hours This Week:
            <?php echo number_format($weekly_hours, 2); ?>
        </p>

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