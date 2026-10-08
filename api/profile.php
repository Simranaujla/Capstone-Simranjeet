<?php
//Start session for API 
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
require_once '../config/database.php';

//tell client that this endpoint returns JSON
header('Content-Type: application/json');

//check if user is logged in 
if(!isset($_SESSION['user_id'])){
    http_response_code(401);

    echo json_encode([
        "message" => "User is not logged in."

    ]);

    exit();
}

//Check session timeout (30 minutes)
if(isset($_SESSION['last_activity'])){
    $inactive_time = time() - $_SESSION['last_activity'];

    if($inactive_time >= 1800){
        session_unset();
        session_destroy();

        http_response_code(401);

        echo json_encode([
            "message" => "Session expired. Please log in again."
        ]);

        exit();
    }

}

//Update last activity time 
$_SESSION['last_activity'] = time();

//ONly allow PUT requests
if($_SERVER["REQUEST_METHOD"] !== "PUT"){
    http_response_code(405);
    echo json_encode([
        "message" => "Only PUT requests are allowed."

    ]);

    exit();
}

//Read JSON data sent from Postman
$data = json_decode(file_get_contents("php://input"),true);

//Check if JSON data is valid 
if(!is_array($data) || json_last_error() !== JSON_ERROR_NONE){
    http_response_code(400);

    echo json_encode([
        "message" => "Invalid JSON data. "
    ]);
    exit();
}

//Get profile information from JSON data 
$full_name = trim($data['full_name']?? '');
$email = trim($data['email']?? '');
$phone = trim($data['phone'] ?? '');


//Validate required fields
if(empty($full_name) || empty($email) || empty($phone)){
    http_response_code(400);

    echo json_encode([
        "message" => "Name,email and phone number are required."
    ]);

    exit();
}

// Validate full name
if (!preg_match("/^[a-zA-Z ]+$/", $full_name)) {
    http_response_code(400);

    echo json_encode([
        "message" => "Name can only contain letters and spaces."
    ]);

    exit();
}

//Validate phone number 
if(!preg_match("/^[0-9]{10}$/", $phone)){
    http_response_code(400);

    echo json_encode([
        "message" => "Please enter a valid 10 digit phone number."

    ]);


    exit();

}

//Validate email format 
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    http_response_code(400);

    echo json_encode([
        "message" => "Please enter a valid email address."
    ]);

    exit();
}

//Check if email is already used by another user
$check_sql = "SELECT user_id
             FROM users
             WHERE email = ?
             AND user_id != ?";

$check_stmt = mysqli_prepare($conn,$check_sql);

mysqli_stmt_bind_param(
    $check_stmt,
    "si",
    $email,
    $_SESSION['user_id']
);

mysqli_stmt_execute($check_stmt);

$check_result = mysqli_stmt_get_result($check_stmt);

if(mysqli_num_rows($check_result) > 0){
    http_response_code(409);

    echo json_encode([
        "message" => "Email is already in use."
    ]);

    exit();
}



// Update profile information
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
    $_SESSION['user_id']
);

//Execute profile update 

if(mysqli_stmt_execute($update_stmt)){
    http_response_code(200);

    echo json_encode([
        "message" => "Profile updated successfully."
    ]);
}

else{
    http_response_code(500);

    echo json_encode([
        "message" => "Unable to update profile."
    ]);
}