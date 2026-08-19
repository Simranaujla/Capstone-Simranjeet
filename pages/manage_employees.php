<?php

require_once '../config/session_check.php';
require_once '../config/database.php';

//Get employee search text
$search="";
if (isset($_GET['search'])){
    $search = trim($_GET['search']);
}

//Restrict access to Employer accounts

if (!isset($_SESSION['user_id'])  || $_SESSION['role_id'] != 1){
    header("Location: dashboard.php");
    exit();
}


if(!empty($search)){
    $sql = "SELECT
    user_id,
    full_name,
    email,
    phone,
    account_status
    FROM users
    WHERE role_id = 2
    AND (full_name LIKE ? OR email LIKE ? )
    ORDER BY full_name";

    $stmt = mysqli_prepare($conn,$sql);

    $search_term = "%". $search . "%";

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $search_term,
        $search_term
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

}
else{
    //Fetch employee profile information
    $sql = "SELECT
    user_id,
    full_name,
    email,
    phone,
    account_status
    FROM users
    WHERE role_id = 2
    ORDER BY full_name";

    $result = mysqli_query($conn,$sql);
}


?>


<!DOCTYPE html>
<html>
    <head>
        <title>Manage Employees</title>
    </head>

    <body>
        
        <h1>Manage Employees</h1>
        <a href="create_employee.php">Create New Employee</a><br><br>
        
        <form method="GET">
            <input type="text" name="search" placeholder = "Search by name or email" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']): ' '; ?>">

            <button type="submit">Search</button>


        </form>
        <br>

        <table border = "1" cellpadding = "8">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

      
        
        <?php

        //Display employee profiles
        while ($row = mysqli_fetch_assoc($result)){
            ?>
            <tr>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['account_status']; ?></td>
                <td>
                    <a href="edit_employee.php?id=<?php echo $row['user_id']; ?>">Edit</a>

                    |

                    <?php if ($row['account_status'] == "Active") { ?>

                        <a href="update_employee_status.php?id=<?php echo $row['user_id']; ?>&status=Inactive" onclick="return confirm('Are you sure you want to deactivate this employee?');">
                            Deactivate
                        </a>

                    <?php } else { ?>

                        <a href="update_employee_status.php?id=<?php echo $row['user_id']; ?>&status=Active" onclick="return confirm('Are you sure you want to activate this employee?');">
                        Activate
                        </a>

                    <?php } ?>
                </td>
            </tr>

        <?php
        }

        ?>

        </table>

        <br>
        <a href = "dashboard.php"> Back to Dashboard </a>


     
     </body>


</html>