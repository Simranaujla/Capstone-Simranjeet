
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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Login - Employee Time Tracking System</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
        <div class="container py-5">

            <h1 class="text-center mb-4">Employee Time Tracking System</h1>

            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">

                            <h2 class="text-center mb-4">User Login</h2>

                            <?php if (!empty($message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">

                                <div class="mb-3">
                                    <label class="form-label">Email or Phone</label>
                                    <input
                                        type="text"
                                        name="email"
                                        class="form-control"
                                        placeholder="Enter your email or phone number"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Enter your password"
                                        required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    Login
                                </button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
