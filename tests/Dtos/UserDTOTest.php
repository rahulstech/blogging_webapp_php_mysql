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
        $this->assertEquals($values["userId"],$dto->userId,"userId not set");
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
            "password" => "pass@123",
            "username" => "testuser1",
            "firstName" => "FirstName1",
            "lastName" => "LastName1",
            "email" => "email1@domain.com",
            "joinedOn" => new DateTime("2022-10-09 12:56:55")
        ));
        $dto = new UserDTO($user);
        $this->assertEquals($user->getUserId(),$dto->userId,"userId not set");
        $this->assertEquals($user->getUsername(),$dto->username,"username not set");
        $this->assertEquals($user->getPasswordHash(),$dto->passwordHash,"passwordHash not set");
        $this->assertEquals($user->getFirstName(),$dto->firstName,"firstName not set");
        $this->assertEquals($user->getLastName(),$dto->lastName,"lastName not set");
        $this->assertEquals($user->getEmail(),$dto->email,"email not set");
        $this->assertEquals($user->getJoinedOn(),$dto->joinedOn,"joinedOn not set");
    }

    /** @test */
    public function isValidPassword(): void 
    {
        $valid = "p@55M0r6";
        $invalidOnlyLC = "abcdefgh";
        $invalidOnlyUC = "ABCDEFGH";
        $invalidOnlyN = "12345678";
        $invalidOnlyS = "#?!@$%^&*-";
        $invalidOnlyLen = "aM$2";
        $invalidNoLC = "ABCDEF#2";
        $invalidNoUC = "abcdef?5";
        $invalidNoN = "ABCdefGh&$";
        $invalidNoS = "ABCdefGh78";
        $invalidOtherS = "ABCdefGh~2";

        $dto = new UserDTO(array("password" => $valid));
        $this->assertTrue($dto->isValidPassword(),"valid check fail");
        $dto = new UserDTO(array("password" => $invalidOnlyLC));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check only lowercase");
        $dto = new UserDTO(array("password" => $invalidOnlyUC));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check only uppercase");
        $dto = new UserDTO(array("password" => $invalidOnlyN));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check only numeric");
        $dto = new UserDTO(array("password" => $invalidOnlyS));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check only symbolls");
        $dto = new UserDTO(array("password" => $invalidOnlyLen));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check only length");
        $dto = new UserDTO(array("password" => $invalidNoLC));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check no lowercase");
        $dto = new UserDTO(array("password" => $invalidNoUC));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check no uppercase");
        $dto = new UserDTO(array("password" => $invalidNoN));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check no numeric");
        $dto = new UserDTO(array("password" => $invalidNoS));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check no symbolls");
        $dto = new UserDTO(array("password" => $invalidOtherS));
        $this->assertNotTrue($dto->isValidPassword(),"invalid check other symbolls");
    }
}
