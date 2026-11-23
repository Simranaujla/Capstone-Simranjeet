
<?php

use PHPUnit\Framework\TestCase;

class AnnouncementTest extends TestCase
{
    // Test that an announcement can be saved in the database
    public function testAnnouncementStorage()
    {
        require __DIR__ . '/../config/database.php';

        $title = "PHPUnit Test Announcement";
        $message = "Testing announcement storage.";

        // Find an employer account for the test
        $result = mysqli_query($conn, "SELECT user_id FROM users WHERE role_id = 1 LIMIT 1");
        $employer = mysqli_fetch_assoc($result);

        $this->assertNotNull($employer);

        $userId = $employer['user_id'];

        // Insert a test announcement
        $sql = "INSERT INTO announcements (title, message, created_by) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "ssi", $title, $message, $userId);

        try {
            $this->assertTrue(mysqli_stmt_execute($stmt));

            $announcementId = mysqli_insert_id($conn);

            // Check that the announcement was saved
            $check = mysqli_prepare($conn, "SELECT title, message FROM announcements WHERE announcement_id = ?");
            mysqli_stmt_bind_param($check, "i", $announcementId);
            mysqli_stmt_execute($check);

            $result = mysqli_stmt_get_result($check);
            $announcement = mysqli_fetch_assoc($result);

            $this->assertNotFalse($announcement);
            $this->assertEquals($title, $announcement['title']);
            $this->assertEquals($message, $announcement['message']);

            mysqli_stmt_close($check);
        } finally {
            // Remove test data after testing
            if (!empty($announcementId)) {
                $delete = mysqli_prepare($conn, "DELETE FROM announcements WHERE announcement_id = ?");
                mysqli_stmt_bind_param($delete, "i", $announcementId);
                mysqli_stmt_execute($delete);
                mysqli_stmt_close($delete);
            }

            mysqli_stmt_close($stmt);
        }
    }
}
