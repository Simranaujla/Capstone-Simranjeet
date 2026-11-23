
<?php

use PHPUnit\Framework\TestCase;

class SortingTest extends TestCase
{
    // Test sorting records by date and total hours
    public function testRecordSorting()
    {
        require __DIR__ . '/../config/database.php';

        $sortOptions = [
            "date_desc" => ["work_date", "DESC"],
            "date_asc" => ["work_date", "ASC"],
            "hours_desc" => ["total_hours", "DESC"],
            "hours_asc" => ["total_hours", "ASC"]
        ];

        foreach ($sortOptions as $option => $sorting) {

            $column = $sorting[0];
            $direction = $sorting[1];

            // Get records in the selected order
            $sql = "SELECT work_date, total_hours
                    FROM time_records
                    ORDER BY $column $direction";

            $result = mysqli_query($conn, $sql);

            $this->assertNotFalse($result);

            $records = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Check that the records are in the correct order
            for ($i = 1; $i < count($records); $i++) {

                $previous = $records[$i - 1][$column];
                $current = $records[$i][$column];

                if ($direction == "ASC") {
                    $this->assertLessThanOrEqual($current, $previous);
                }
                else {
                    $this->assertGreaterThanOrEqual($current, $previous);
                }
            }
        }
    }
}
