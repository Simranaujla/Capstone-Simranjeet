<?php
session_start();
require_once '../config/database.php';


//Restrict access to Employer accounts
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location: dashboard.php");
    exit();
}


$user_id = $_GET['id'];

//Fetch employee profile information
$sql = "SELECT full_name,email,phone
FROM users
WHERE user_id = ? ";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

//Process employee profile update
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);


    //Update emloyee profile users
    $update_sql = "UPDATE users
    SET full_name = ?,
    email = ?,
    phone = ?
    WHERE user_id = ?";

    $update_stmt = mysqli_prepare($conn,$update_sql);

    mysqli_stmt_bind_param(
        $update_stmt,
        "sssi",
        $full_name,
        $email,
        $phone,
        $user_id
    );


    if(mysqli_stmt_execute($update_stmt)){
        $message = "Employee profile updated successfully." ;
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
    }
    else {
        $message = mysqli_error($conn);
    }
}

?>


<!DOCTYPE html>
<html>
    <head>
        <title>Edit Employee</title>
    </head>

    <body>
        <h1>Edit Employee</h1>

        <?php if (!empty($message)){ ?>
            <p><?php echo $message; ?></p>

        <?php } ?>

        <form method="POST">
            <label>Full Name</label><br>
            <input type="text"
            name="full_name"
            value="<?php echo $user['full_name']; ?>"><br><br>

            <label>Email</label><br>
            <input type="email"
            name="email"
            value="<?php echo $user['email']; ?>"><br><br>

            <label>Phone</label><br>
            <input type="text"
            name="phone"
            value="<?php echo $user['phone']; ?>"> <br><br>


            <button type="submit">Update Employee</button>


        </form>

        <br>

        <a href = "manage_employees.php">Back</a>

    </body>
</html>









