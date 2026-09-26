<?php
    
    require_once '../config/session_check.php';
    require_once '../config/database.php';

    //Verify user login session
    if (!isset ($_SESSION['user_id'])){
        header("Location:login.php");
        exit();
    }

    $message ="";
    //Process profile update request
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);



        // Validate required profile fields
        if(empty($full_name) || empty($email) || empty($phone) ){
            $message = "Name ,email and phone number are required.";
        }

        //Validate full name 
        elseif(!preg_match("/^[a-zA-Z ]+$/", $full_name)){
            $message = "Name can only contain letter and spaces";
        }

        //Validate phone number format
        elseif (!preg_match("/^[0-9]{10}$/",$phone)){
            $message = "Please enter a valid 10 digit phone number.";
        }
        
        //Validate email format
        elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $message = "Please enter a valid email address.";
        }

        else{
            //Check if email already exists
            $check_sql = "SELECT user_id
            FROM users
            WHERE email= ?
            AND user_id != ?";
            
            $check_stmt =mysqli_prepare($conn,$check_sql);

            mysqli_stmt_bind_param(
                $check_stmt,
                "si",
                $email,
                $_SESSION['user_id']
            );

            mysqli_stmt_execute($check_stmt);

        
            $check_result = mysqli_stmt_get_result($check_stmt);

            if(mysqli_num_rows($check_result)>0){
                $message = "Email is already in use.";
            }

            else{
                //Update employee profile information
                $update_sql = "UPDATE users 
                SET full_name = ? ,
                email = ?,
                phone =? 
                WHERE user_id = ?";


                $update_stmt =
                mysqli_prepare($conn, $update_sql);

                mysqli_stmt_bind_param(
                    $update_stmt,
                    "sssi",
                    $full_name,
                    $email,
                    $phone,
                    $_SESSION['user_id']
                );

                if (mysqli_stmt_execute($update_stmt)){
                    $message = "Profile updated successfully";
                } else{
                    $message = mysqli_error($conn);
                }
            }
        }
    }

    //Fetch employee profile information

    $sql = "SELECT full_name,email,phone
    FROM users 
    WHERE user_id = ?";

    $stmt = mysqli_prepare($conn,$sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $_SESSION['user_id']

    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    ?>


<!DOCTYPE html >
<html>
    <head>
        <title>Profile</title>
    </head>

    <body>
        <h1>My Profile</h1>
        <p><?php echo $message;?><p>
        <form method="POST">
            <label>Full Name</label> <br>
            <input type = "text" name ="full_name" value = "<?php echo $user['full_name']; ?>" ><br><br>

            <input type = "email" name ="email" value = "<?php echo $user['email']; ?>" ><br><br>

            <input type = "text" name ="phone" value = "<?php echo $user['phone']; ?>" ><br><br>

            <button type = "submit"> Update Profile </button>
        </form>
        <br>
        
        <a href="change_password.php">Change Password</a>
        <a href = "dashboard.php">Back to Dashboard </a>




        
    </body>
</html>