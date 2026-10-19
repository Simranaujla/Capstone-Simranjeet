<?php
  // Use environment variables for deployment
  // Keep MAMP settings as defaults for local development
  $host = getenv("DB_HOST") ?: "localhost";
  $username = getenv("DB_USER") ?: "root";
  $password = getenv("DB_PASSWORD") ?: "root";
  $database = getenv("DB_NAME") ?: "employee_time_tracking";

  $conn = mysqli_connect($host, $username, $password, $database);

  try {
    $conn = mysqli_connect($host, $username, $password, $database);
  } catch (mysqli_sql_exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(503);
    die("Database temporarily unavailable.");
  }
?>