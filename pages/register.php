<?php

// Database connection
require_once '../config/database.php';

$message = "";

// Check if registration form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role_id = $_POST['role_id'];

    // Validate required fields
    if (
        empty($full_name) ||
        empty($email) ||
        empty($phone) ||
        empty($password)
    ) {

        $message = "All fields are required.";

    } else {

        // Validate phone number
        if (!preg_match('/^\d{10}$/', $phone)) {
            $message = "Phone number must contain exactly 10 digits.";
        }
        else{

            // Validate password strength
            $password_pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W]).{8,}$/";

            if (!preg_match($password_pattern, $password)) {

                $message = "Password must contain at least 8 characters, uppercase, lowercase, number, and special character.";

            } else {

                // Check if email already exists
                $check_email_sql = "SELECT * FROM users WHERE email = ?";

                $check_stmt = mysqli_prepare($conn, $check_email_sql);

                mysqli_stmt_bind_param($check_stmt, "s", $email);

                mysqli_stmt_execute($check_stmt);

                $email_result = mysqli_stmt_get_result($check_stmt);

                if (mysqli_num_rows($email_result) > 0) {

                    $message = "Email already exists.";

                } else {

                    // Hash password before storing in database
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insert user into database
                    $sql = "INSERT INTO users 
                    (role_id, full_name, email, phone, password_hash)
                    VALUES (?, ?, ?, ?, ?)";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "issss",
                        $role_id,
                        $full_name,
                        $email,
                        $phone,
                        $hashed_password
                    );

                    // Execute query
                    if (mysqli_stmt_execute($stmt)) {

                        $message = "Registration successful.";

                    } else {

                        $message = mysqli_error($conn);
                    }
                }
            }
        }
    }
}


?>



<!DOCTYPE html>
<html>
    <head>
        <title>Register</title>
    </head>
    <body>
        <h1>Employee Time Tracking System</h1>
        <p><?php echo $message; ?></p>

        <form method="POST">
            <label>Full Name</label><br>
            <input type="text" name="full_name"><br><br>

            <label>Email</label><br>
            <input type="email" name="email"><br><br>

            <label>Phone Number</label><br>
            <input type="text" name="phone"><br><br>

            <label>Password</label><br>
            <input type="password" name="password"><br><br>

            <label>Role</label><br>
            <select name="role_id">
                <option value="2">Employee</option>
                <option value="1">Employer</option>
            </select><br><br>

            <button type="submit">Register</button>

        </form>
    </body>
</html>
