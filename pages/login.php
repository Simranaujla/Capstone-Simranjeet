<?php

// Start session
session_start();

// Database connection
require_once '../config/database.php';

$message = "";

// Check if login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $login = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Find user by email or phone
    $sql = "SELECT * FROM users
            WHERE email = ?
            OR phone = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $login, $login);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    // Check if user exists
    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // Check if account is currently locked
        if (
            $user['locked_until'] !== NULL &&
            strtotime($user['locked_until']) > time()
        ) {

            $message = "Your account is temporarily locked. Please try again later.";

        }

        // Verify password
        elseif (password_verify($password, $user['password_hash'])) {

            // Reset failed login attempts
            $reset_sql = "UPDATE users
                          SET failed_login_attempts = 0,
                              locked_until = NULL
                          WHERE user_id = ?";

            $reset_stmt = mysqli_prepare($conn, $reset_sql);
            mysqli_stmt_bind_param(
                $reset_stmt,
                "i",
                $user['user_id']
            );
            mysqli_stmt_execute($reset_stmt);

            // Store session data
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role_id'] = $user['role_id'];

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();

        }

        // Incorrect password
        else {

            // Increase failed attempts
            $failed_attempts = $user['failed_login_attempts'] + 1;

            // Lock after 5 failed attempts
            if ($failed_attempts >= 5) {

                // Lock for 15 minutes
                $locked_until = date(
                    "Y-m-d H:i:s",
                    strtotime("+15 minutes")
                );

                $lock_sql = "UPDATE users
                             SET failed_login_attempts = ?,
                                 locked_until = ?
                             WHERE user_id = ?";

                $lock_stmt = mysqli_prepare($conn, $lock_sql);

                mysqli_stmt_bind_param(
                    $lock_stmt,
                    "isi",
                    $failed_attempts,
                    $locked_until,
                    $user['user_id']
                );

                mysqli_stmt_execute($lock_stmt);

                $message = "Too many failed login attempts. Your account is locked for 15 minutes.";

            }

            // Account not locked yet
            else {

                $attempts_remaining = 5 - $failed_attempts;

                $update_sql = "UPDATE users
                               SET failed_login_attempts = ?
                               WHERE user_id = ?";

                $update_stmt = mysqli_prepare($conn, $update_sql);

                mysqli_stmt_bind_param(
                    $update_stmt,
                    "ii",
                    $failed_attempts,
                    $user['user_id']
                );

                mysqli_stmt_execute($update_stmt);

                $message = "Invalid password. You have "
                         . $attempts_remaining
                         . " attempts remaining.";
            }
        }

    }

    // User does not exist
    else {

        $message = "Invalid email/phone or password.";
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
