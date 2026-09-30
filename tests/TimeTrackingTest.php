<?php 
use PHPUnit\Framework\TestCase;

class TimeTrackingTest extends TestCase{
    //TC-U1: Test total hours calculation
    public function testCalculateEightHourShift(){
        $punch_in = "2026-09-28 09:00:00";
        $punch_out = "2026-09-28 17:00:00";

        $hours = (
            strtotime($punch_out) - strtotime($punch_in)
        ) / 3600;

        $this->assertEquals(8,$hours);
    }
}

