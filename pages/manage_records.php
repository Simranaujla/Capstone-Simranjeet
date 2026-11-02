
<?php

require_once '../config/session_check.php';
require_once '../config/database.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verify user login session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Restrict access to Employer only
if ($_SESSION['role_id'] != 1) {
    header("Location: dashboard.php");
    exit();
}

// Get employee search value
$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Get filter work records
$start_date = "";
$end_date = "";

if (isset($_GET['start_date'])) {
    $start_date = $_GET['start_date'];
}

if (isset($_GET['end_date'])) {
    $end_date = $_GET['end_date'];
}

// Get sorting option
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

// Set sorting based on selected option
if ($sort == 'date_asc') {
    $order_by = "time_records.work_date ASC";
}
elseif ($sort == 'hours_desc') {
    $order_by = "time_records.total_hours DESC";
}
elseif ($sort == 'hours_asc') {
    $order_by = "time_records.total_hours ASC";
}
else {
    $order_by = "time_records.work_date DESC";
}

// Prepare employee name for searching
$search_name = "%" . $search . "%";

// Fetch all employee work records
if (!empty($start_date) && !empty($end_date)) {

    $sql = "SELECT
                time_records.record_id,
                users.full_name,
                time_records.work_date,
                time_records.punch_in,
                time_records.punch_out,
                time_records.total_hours,
                time_records.approval_status
            FROM time_records
            INNER JOIN users
                ON time_records.user_id = users.user_id
            WHERE time_records.work_date BETWEEN ? AND ?
                AND users.full_name LIKE ?
            ORDER BY $order_by";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $start_date,
        $end_date,
        $search_name
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

}
else {

    $sql = "SELECT
                time_records.record_id,
                users.full_name,
                time_records.work_date,
                time_records.punch_in,
                time_records.punch_out,
                time_records.total_hours,
                time_records.approval_status
            FROM time_records
            INNER JOIN users
                ON time_records.user_id = users.user_id
            WHERE users.full_name LIKE ?
            ORDER BY $order_by";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $search_name
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Manage Records - Employee Time Tracking System</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
        <main class="container py-4">

            <h1 class="mb-4">Employee Work Records</h1>

            <!-- Work records date filter -->
            <form method="GET">

                <div class="mb-3">
                    <label class="form-label">Search Employee</label>
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Enter employee name"
                        value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label">From:</label>
                        <input
                            type="date"
                            name="start_date"
                            class="form-control"
                            value="<?php echo htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">To:</label>
                        <input
                            type="date"
                            name="end_date"
                            class="form-control"
                            value="<?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            Filter
                        </button>
                    </div>

                </div>

                <input
                    type="hidden"
                    name="sort"
                    value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">

            </form>

            <br>

            <!-- Sort employee records -->
            <form method="GET">

                <input
                    type="hidden"
                    name="search"
                    value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">

                <input
                    type="hidden"
                    name="start_date"
                    value="<?php echo htmlspecialchars($start_date, ENT_QUOTES, 'UTF-8'); ?>">

                <input
                    type="hidden"
                    name="end_date"
                    value="<?php echo htmlspecialchars($end_date, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label">Sort by:</label>

                        <select name="sort" class="form-select">
                            <option value="date_desc" <?php if ($sort == 'date_desc') echo 'selected'; ?>>
                                Date: Newest First
                            </option>

                            <option value="date_asc" <?php if ($sort == 'date_asc') echo 'selected'; ?>>
                                Date: Oldest First
                            </option>

                            <option value="hours_desc" <?php if ($sort == 'hours_desc') echo 'selected'; ?>>
                                Hours: Highest First
                            </option>

                            <option value="hours_asc" <?php if ($sort == 'hours_asc') echo 'selected'; ?>>
                                Hours: Lowest First
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            Sort
                        </button>
                    </div>

                </div>

            </form>

            <br>

            <!-- Employee records table -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Date</th>
                            <th>Punch In</th>
                            <th>Punch Out</th>
                            <th>Total Hours</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars((string) $row['work_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars((string) $row['punch_in'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars((string) $row['punch_out'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars((string) $row['total_hours'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars((string) $row['approval_status'], ENT_QUOTES, 'UTF-8'); ?></td>

                                <td>
                                    <form method="POST" action="update_status.php" class="d-inline">
                                        <input type="hidden"
                                               name="id"
                                               value="<?php echo (int) $row['record_id']; ?>">


                                        <input type="hidden"
                                                name="status"
                                                value="Approved">
                                        <input type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                                        
                                        <button type="submit"
                                                class="btn btn-success btn-sm"
                                                onclick="return confirm('Approve this work record?')">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST" action="update_status.php" class="d-inline">
                                        <input type="hidden"
                                               name = "id"
                                               value="<?php echo (int) $row['record_id']; ?>">

                                        <input type="hidden"
                                               name = "status"
                                               value="Rejected">
                                        
                                        <input type="hidden"
                                               name = "csrf_token"
                                               value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                                        <button type="submit"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Reject this work record?')">
                                               Reject
                                        </button>
                                    </form>


                                    
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>

                </table>
            </div>

            <a href="dashboard.php">Back to Dashboard</a>

        </main>
    </body>
</html>
