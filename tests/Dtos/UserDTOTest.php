<?php

namespace Rahulstech\Blogging\Tests\Dtos;
use DateTime;
use PHPUnit\Framework\TestCase;
use Rahulstech\Blogging\Dtos\UserDTO;
use Rahulstech\Blogging\Entities\User;

class UserDTOTest extends TestCase
{ 
    /** @test */
    public function valueFormInput(): void 
    {
        $values = array(
            "userId" => 1,
            "password" => "pass@123",
            "username" => "testuser1",
            "firstName" => "FirstName1",
            "lastName" => "LastName1",
            "email" => "email1@domain.com"
        );
        $dto = new UserDTO($values);
        $this->assertEquals($values["username"],$dto->username,"username not set");
        $this->assertEquals($values["password"],$dto->password,"password not set");
        $this->assertEquals($values["firstName"],$dto->firstName,"firstName not set");
        $this->assertEquals($values["lastName"],$dto->lastName,"lastName not set");
        $this->assertEquals($values["email"],$dto->email,"email not set");
    }

    /** @test */
    public function valueUserObject(): void 
    {
        $user = User::createNewFromArray(array(
            "userId" => 1,
            "passwordHash" => "pass@123",
            "username" => "testuser1",
            "firstName" => "FirstName1",
            "lastName" => "LastName1",
            "email" => "email1@domain.com",
            "joinedOn" => new DateTime("2022-10-09 12:56:55")
        ));
        $dto = new UserDTO($user);
        $this->assertEquals($user->getUsername(),$dto->username,"username not set");
        $this->assertEquals($user->getPasswordHash(),$dto->passwordHash,"passwordHash not set");
        $this->assertEquals($user->getFirstName(),$dto->firstName,"firstName not set");
        $this->assertEquals($user->getLastName(),$dto->lastName,"lastName not set");
        $this->assertEquals($user->getEmail(),$dto->email,"email not set");
    }

    /** @test */
    public function checkPasswordStrength(): void 
    {
        $dto = new UserDTO(array("password" => "123"));
        $this->assertNotTrue($dto->checkPasswordStrength(),"password length");
        $dto = new UserDTO(array("password" => "abcdefgh"));
        $this->assertNotTrue($dto->checkPasswordStrength(),"password only a-z");
        $dto = new UserDTO(array("password" => "abcdEFGH"));
        $this->assertNotTrue($dto->checkPasswordStrength(),"password only a-zA-Z");
        $dto = new UserDTO(array("password" => "abCD1234"));
        $this->assertNotTrue($dto->checkPasswordStrength(),"password only a-zA-Z0-9");
        $dto = new UserDTO(array("password" => "abCD~123"));
        $this->assertNotTrue($dto->checkPasswordStrength(),"password non accepted symboll: ");
        $dto = new UserDTO(array("password" => "aBcD#123"));
        $this->assertTrue($dto->checkPasswordStrength(),"password valid");
    }

    
    /** @test */
    public function checkEmail(): void 
    {
        $dto = new UserDTO(array("email" => "email.12@gmail.co.in"));
        $this->assertTrue($dto->checkEmail(),"valid email");
        $dto = new UserDTO(array("email" => ".email@gmail.com"));
        $this->assertNotTrue($dto->checkEmail(),"username starts with '.'");
        $dto = new UserDTO(array("email" => "email.@gmail.com"));
        $this->assertNotTrue($dto->checkEmail(),"username ends with '.'");
        $dto = new UserDTO(array("email" => "email@.com"));
        $this->assertNotTrue($dto->checkEmail(),"no host");
        $dto = new UserDTO(array("email" => "email@gmail"));
        $this->assertNotTrue($dto->checkEmail(),"no domain");
        $dto = new UserDTO(array("email" => "email@gmail."));
        $this->assertNotTrue($dto->checkEmail(),"host ends with '.'");
    }

    /** @test */
    public function validateSignUp(): void 
    {
        $dto = new UserDTO(array(
            "password" => "pass@123",
            "confirmPassword" => "pass#123",
            "email" => "email@host.com"
        ));
        $this->assertNotTrue($dto->validateSignUp(),"password != confirmPassword");
        $dto = new UserDTO(array(
            "password" => 'pass@123',
            "confirmPassword" => 'pass@123',
            "email" => "email@host.com"
        ));
        $this->assertTrue($dto->validateSignUp(),"valid inputs");
    }
}
