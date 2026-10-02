<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase

{
    //test database connection
    public function testDatabaseConnection(){
    
        require __DIR__ . '/../config/database.php';
        $this->assertNotFalse($conn);
    }
}