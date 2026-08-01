<?php
session_start();
require_once '../config/database.php';

//Restrict access to Employer accounts
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location:dashboard.php");
    exit();
}


$message = "";

//Process employee creation form
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    //Validate required employee fields
    if(empty($full_name) || empty($email) || empty($phone) || empty($password)){
        $message = "All fields are required.";
    }

    //Validate employee name
    elseif(!preg_match("/^[a-zA-Z ]+$/", $full_name)){
        $message = "Name can only contain letters and spaces.";
    }

    //Validate email format
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $message = "Please enter a valid email address.";
    }


    //Validate phone number
    elseif(!preg_match("/^[0-9]{10}$/",$phone)){
        $message = "Please enter a valid 10 digit phone number.";
    }

    // Validate password strength
    
    elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W]).{8,}$/", $password)) {

        $message = "Password must contain at least 8 characters, uppercase, lowercase, number, and special character.";

    }

    else{
        // Check if email already exists
        $check_sql = "SELECT user_id
        FROM users
        WHERE email = ?";

        $check_stmt = mysqli_prepare($conn,$check_sql);

        mysqli_stmt_bind_param(
            $check_stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);
        if(mysqli_num_rows($check_result) >0 ){
            $message = "Email already exists.";
        }

        else{
            //Encrypt employee password
            $hashed_password = password_hash($password,PASSWORD_DEFAULT);

            //Create employee account
            $insert_sql = "INSERT INTO users
            (full_name,email,phone,password,role_id,account_status)
            VALUES (?,?,?,?,2,'Active')";

            $insert_stmt = mysqli_prepare($conn,$insert_sql);

            mysqli_stmt_bind_param(
                $insert_stmt,
                "ssss",
                $full_name,
                $email,
                $phone,
                $hashed_password
            );

            if (mysqli_stmt_execute($insert_stmt)){
                $message = "Employee created successfully.";
                header("Refresh:2; url=manage_employees.php");
            }
            else{
                $message = mysqli_error($conn);
            }
        }

    } 



}
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
            <input type="text" name="full_name" value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>"><br><br>

            <label>Email</label><br>
            <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"><br><br>

            <label>Phone</label><br>
            <input type="text" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>"><br><br>

            <label>Password</label><br>
            <input type="password" name="password"><br><br>

            <button type="submit">Create Employee</button>

        </form>
        <br>

        <a href="manage_employees.php">Back to Manage Employees </a>

        
    </body>
</html>