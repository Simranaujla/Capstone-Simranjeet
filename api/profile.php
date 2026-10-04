<?php
require_once '../config/session_check.php';
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



