<?php
declare(strict_types=1);

namespace Rahulstech\Blogging\Tests\Repositories;

use Rahulstech\Blogging\Entities\User;
use Rahulstech\Blogging\Repositories\UserRepo;
use Rahulstech\Blogging\Tests\DatabaseTestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserRepoTest extends DatabaseTestCase
{
    private UserRepo $userRepo;

    protected function setUp(): void {
        parent::setUp();
        $this->userRepo = $this->getEntityManager()->getRepository(User::class);
    }

    /** @test */
    public function createUser(): void 
    {
        $user = User::createNewFromArray(array(
            "username" => "user1",
            "firstName" => "FirstName11",
            "lastName" => "LastName11",
            "email" => "email11@domain.com",
            "passwordHash" => "pass$456"
        ));
        $created = $this->userRepo->save($user);
        $this->assertTrue($created,"user not created");
    }

    /** @test */
    public function getExistingUserByUsername(): void
    {
        $username = "testuser1";
        $testuser1 = $this->userRepo->getByUsername($username);
        $this->assertNotNull($testuser1,"existing user not fetched");
        $this->assertEquals($username,$testuser1->getUsername(),"fetched user with different username");
    }

    /** @test */
    public function getNotExistingUserByUsername(): void
    {
        $username = "nonexistinguser";
        $user = $this->userRepo->find($username);
        $this->assertNull($user);
    }

    /** 
     * @expectedException UniqueConstraintViolationException
    */
    public function createUserWithExistingUsername(): void
    {
        $user = User::createNewFromArray(array(
            "username" => "testuser1"
        ));
        $this->userRepo->save($user);
    }

    /** @test */
    public function updateExistingUser(): void
    {
        $user = User::createNewFromArray(array(
            "userId" => 1,
            "username" => "testuser1_updated",
            "passwordHash" => '$2y$10$cF2PxqnG00nr98OEiSNEre.ARkQAxDFq/n13pjzs4vr37rCEQzvvi',
            "firstName" => "FirstName1",
            "lastName" => "LastName1",
            "email" => "email1@domain.com"
        ));
        $updated = $this->userRepo->save($user);
        $this->assertTrue($updated);
    }

    /** 
     * @expectedException Doctrine\DBAL\Exception\UniqueConstraintViolationException 
     */
    public function updateToExistingUsername(): void
    {
        $user = User::createNewFromArray(array(
            "userId" => 2,
            "username" => "testuser1"
        ));
        $this->userRepo->save($user);
    }

    /** @test */
    public function searchUser(): void {
        $users = $this->userRepo->searchUser("test");
        $this->assertNotNull($users,"no result found");
        $this->assertEquals(3,count($users),"number of results does not match");
    }

    /** @test */
    public function removeExistingUser(): void
    {
        $userId = 1;
        $removed = $this->userRepo->removeUser($userId);
        $this->assertTrue($removed);
    }

    /** @test */
    public function removeNonExistingUser(): void
    {
        $userId = 25;
        $removed = $this->userRepo->removeUser($userId);
        $this->assertNotTrue($removed);
    }

    /** @test */
    public function countPostForUserHavingPosts(): void
    {
        $user = $this->userRepo->find(2);
        $this->assertNotNull($user->getMyPosts(), "no post loadded");
        $this->assertEquals(2,count($user->getMyPosts()),"required number of posts not loaded");
    }
}
