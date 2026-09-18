<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    //Test that a password can be hashed and verified
    public function testPasswordHashing(){
        $password = "Test@1234";
        $hash = password_hash($password,PASSWORD_DEFAULT);
        
        //Stored password should not be the same a plain password
        $this->assertNotEquals($password,$hash);

        //Correct password should match the hash
        $this->assertTrue(
            password_verify($password,$hash)
        );
    }

    //Test that an incorrect password is rejected 
    public function testINvalidPassword(){
        $password = "Test@1234";
        $wrong_password = "Wrong@123";

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->assertFalse(
            password_verify($wrong_password,$hash)
        );

    }

    //Test that the correct passowrd is accepted
    public function testValidPassword(){
        $password = "Test@1234";

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->assertTrue(
            password_verify($password, $hash)
        );
    }

}