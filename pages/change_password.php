<?php
session_start();
require_once '../config/database.php';

//Verify user login session
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$message = "";

//Process password change request
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    //Validate requirede fields
    if(empty($current_password) || empty($new_password) || empty($confirm_password)){
        $message = "All password fields are required.";
    }


    //Check new password and confirmation
    elseif($new_password !== $confirm_password){
        $message = "New password and confirmation password do not match.";
    }

    //Validate password strength
    elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W]).{8,}$/", $new_password)){
        $message = "Password must contain at least 8 characters, uppercase,lowercase,number, and special character.";
    }

    else{
        //Fetch current password 
        $sql = "SELECT password_hash
        FROM users
        WHERE user_id = ? ";

        $stmt = mysqli_prepare($conn,$sql);

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $_SESSION['user_id']
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);

        //Verify current password
        if(!password_verify($current_password,$user['password_hash'])){
            $message = "Current password is incorrect.";
        }

        else{
            //Hash new password
            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );

            //Update password
            $update_sql = "UPDATE users
            SET password_hash = ?
            WHERE user_id = ? ";

            $update_stmt = mysqli_prepare(
                $conn,
                $update_sql
            );

            mysqli_stmt_bind_param(
                $update_stmt,
                "si",
                $hashed_password,
                $_SESSION['user_id']
            );

            if(mysqli_stmt_execute($update_stmt)){
                $message = "Password updated succesfully.";
            }
            else{
                $message = mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CHange Password</title>
</head>
<body>
    <h1>Change Password </h1>

    <?php if(!empty($message)) { ?>
        <p><?php echo htmlspecialchars($message); ?> </p>
    
    <?php } ?>

    <form method = "POST">
        <label>Current Password</label><br>
        <input type="password" name="current_password" required>

        <br><br>

        <label>New Password</label><br>
        <input type="password" name="new_password" required>

        <br><br>

        <label>Confirm New Password</label><br>
        <input type="password" name="confirm_password" required>

        <br><br>

        <button type="submit">Update Password</button>



    </form>

    <a href = "profile.php">Back to Profile </a>
    
    
</body>
</html>