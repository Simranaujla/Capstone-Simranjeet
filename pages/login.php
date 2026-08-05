
<?php
  //Start session
  session_start();
  
  //Database connection
  require_once '../config/database.php';

  $message = "";

  //Check if login form is submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST"){

      //Get form data
      $login = trim($_POST['email']);
      $password = trim($_POST['password']);

      //SQL query to find user by email or phone number
      $sql = "SELECT * FROM users 
            WHERE email = ? 
            OR phone = ? ";

      $stmt = mysqli_prepare($conn,$sql);
      mysqli_stmt_bind_param($stmt, "ss" , $login, $login);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      //Check if user exists
      if (mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);

        //Verify hashed password
        if (password_verify($password, $user['password_hash'])){

             //Store session data 
             $_SESSION['user_id'] = $user['user_id'];
             $_SESSION['full_name'] = $user['full_name'];
             $_SESSION['role_id'] = $user['role_id'];

             header("Location: dashboard.php");
             exit();
        }
        else{
            $message ="Invalid password";
        }
      }
        else{
            $message = "User not found";
        }
      }




?>


<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
    </head>

    <body>
        <h1>Employee time Tracking System</h1>

        <h2>User Login</h2>

        <p><?php echo $message; ?> </p>

        <form method="POST">
            <label>Enter your Email or Phone </label><br>
            <input type="text" name="email" placeholder="Enter your email or phone number"><br><br>

            <label>Password</label><br>
            <input type="password" name="password"><br><br>

            <button type="submit">Login</button>
        </form>
    </body>
</html>
