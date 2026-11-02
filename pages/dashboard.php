<?php
   //Start session
   
   require_once '../config/session_check.php';

   //Check if user is logged in 
   if (!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
   }
?>

<!DOCTYPE html>
<html>
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <title>Dashboard</title>

      <!-- Bootstrap 5 CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
      <!-- Custom CSS -->
      <link rel="stylesheet" href="../assets/css/style.css">
  </head>

    <body>
        <nav class="navbar navbar-dark bg-primary">
         <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                Employee Time Tracking System
            </a>

            <a class="btn btn-outline-light" href="logout.php">
                Logout
            </a>
         </div>
        </nav>

        <main class="container py-4">
        <h1 class="mb-4">Dashboard</h1>

        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p>You are successfully logged in. </p>
        

        <?php
          if ($_SESSION['role_id']== 1){
            echo "<h3>Employer Dashboard</h3>";
            echo "<p>Manage employees and announcements.</p>";
            echo '<div class="row g-3">';

            

            // Manage Employee Records
            echo '<div class="col-md-4">';
            echo '    <div class="card shadow-sm h-100">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">Manage Employee Records</h5>';
            echo '            <p class="card-text">View and approve employee work hours.</p>';
            echo '            <a href="manage_records.php" class="btn btn-primary">View Records</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';

            // Manage Employees
            echo '<div class="col-md-4">';
            echo '    <div class="card shadow-sm h-100">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">Manage Employees</h5>';
            echo '            <p class="card-text">View and manage employee accounts.</p>';
            echo '            <a href="manage_employees.php" class="btn btn-primary">Manage Employees</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';

            // Announcements
            echo '<div class="col-md-4">';
            echo '    <div class="card shadow-sm h-100">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">Announcements</h5>';
            echo '            <p class="card-text">Create and view announcements for employees.</p>';
            echo '            <a href="announcements.php" class="btn btn-primary">View Announcements</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';

            echo '</div>';
          }

          else{
            echo "<h3>Employee Dashboard</h3>";
            echo "<p>View work hours and punch records.</p>";

            echo '<div class="row g-3">';

            // Time Tracking
            echo '<div class="col-md-4">';
            echo '    <div class="card shadow-sm h-100">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">Time Tracking</h5>';
            echo '            <p class="card-text">Punch in, punch out, and view your work hours.</p>';
            echo '            <a href="time_tracking.php" class="btn btn-primary">View Time Tracking</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';

            // My Profile
            echo '<div class="col-md-4">';
            echo '    <div class="card shadow-sm h-100">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">My Profile</h5>';
            echo '            <p class="card-text">View and update your personal information.</p>';
            echo '            <a href="profile.php" class="btn btn-primary">View Profile</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';

            // Announcements
            echo '<div class="col-md-4">';
            echo '    <div class="card shadow-sm h-100">';
            echo '        <div class="card-body">';
            echo '            <h5 class="card-title">Announcements</h5>';
            echo '            <p class="card-text">View important updates from your employer.</p>';
            echo '            <a href="announcements.php" class="btn btn-primary">View Announcements</a>';
            echo '        </div>';
            echo '    </div>';
            echo '</div>';

            echo '</div>';
          }

        ?>
      </main>
    </body>
</html>

