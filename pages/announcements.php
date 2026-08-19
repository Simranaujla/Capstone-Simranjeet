<?php 
    session_start();
    require_once '../config/session_check.php';
    require_once '../config/database.php';
    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();

    }
   

    $message = "";

    //Process announcement form submission
    if ($_SERVER ["REQUEST_METHOD"] == "POST"){
        $title = trim($_POST['title']);
        $announcement = trim($_POST['message']);

        //Validate required announcement fields
        if (empty($title) || empty($announcement)){
            $message = "Title and announcement message are required.";
        }

        else{
            //Save announcement to database
            $sql = "INSERT INTO announcements
            (title,message,created_by)
            VALUES (?,?,?)";

             $stmt = mysqli_prepare($conn,$sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $title,
                $announcement,
                $_SESSION['user_id']
            );
            

            if (mysqli_stmt_execute($stmt)){
                $message = "Announcement posted successfully.";
            }
            else{
                $message = mysqli_error($conn);
            }
        }


    }
    

    //Fetch announcements from database
    $announcement_sql ="SELECT 
    announcements.title,
    announcements.message,
    announcements.created_at,
    users.full_name
    FROM announcements
    INNER JOIN users
    ON announcements.created_by = users.user_id
    ORDER BY announcements.created_at DESC";

    $announcement_result = mysqli_query($conn,$announcement_sql);


?>

<!DOCTYPE html>
<html>
    <head>
        <title>Announcements</title>
    </head>

    <body>
        <?php if ($_SESSION['role_id'] == 1) { ?>
            <h1> Create Announcements</h1>
            <p><?php echo $message; ?></p>
            <form method="POST">
                <label>Title</label><br>
                <input type="text" name="title"><br><br>

                <label>Message</label><br>
                <textarea name="message" rows="5" cols="40" ></textarea><br><br>

                <button type="submit">Post Announcement</button>

            </form>
        <?php } ?>
        <br>

        <h2>Announcements</h2>

        <?php 
          if(mysqli_num_rows($announcement_result)>0){
            while ($row = mysqli_fetch_assoc($announcement_result)) { ?>
                <h3><?php echo $row['title']; ?></h3>

                <p><?php echo $row['message']?></p>

                <small>
                    Posted by:
                    <?php echo $row['full_name']; ?>
                    <?php echo $row['created_at']; ?>
                </small>
                <hr>
        <?php
            }
          } else{
            echo"<p> No announcement available.</p>";
          } 
        ?>

        <a href="dashboard.php">Back to Dashboard</a>
    </body> 
</html>

