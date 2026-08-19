<?php 

//Start session 
if (session_status() === PHP_SESSION_NONE){
    session_start();
}

//Session timeout: 30 minutes
$session_timeout = 1800;

//Check if user is logged in 
if(!isset($_SESSION['user_id'])){
    header("Location: ../pages/login.php");
    exit();
}

//Check insctive time
if (isset($_SESSION['last_activity'])){
    $inactive_time = time() - $_SESSION['last_activity'];

    if($inactive_time >= $session_timeout){
        //Destroy expired session
        session_unset();
        session_destroy();

        header("Location: ../pages/login.php?timeout=1");
        exit();
    }
}

//Update last activity time
$_SESSION['last_activity'] = time();

?>

