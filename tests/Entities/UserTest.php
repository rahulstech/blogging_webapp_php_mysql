<?php

namespace Rahulstech\Blogging\Tests\Entities;
use PHPUnit\Framework\TestCase;
use Rahulstech\Blogging\Entities\User;

class UserTest extends TestCase
{
    /** @test */
    public function checkPassword(): void 
    {
        $correctPassword = "pass$123";
        $wrongPassword = "45kh^^$$";
        $user = User::createNewFromArray(array("passwordHash" => $correctPassword));
        $this->assertTrue($user->checkPassword($correctPassword),"correct password check fail");
        $this->assertNotTrue($user->checkPassword($wrongPassword),"wrong password check fail");
    }
}
