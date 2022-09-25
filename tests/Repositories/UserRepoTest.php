<?php
declare(strict_types=1);

namespace Rahulstech\Blogging\Tests\Repositories;

use Rahulstech\Blogging\Entities\User;
use Rahulstech\Blogging\Repositories\UserRepo;
use Rahulstech\Blogging\Tests\DatabaseTestCase;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserRepoTest extends DatabaseTestCase
{
    private UserRepo $repo;

    protected function setUp(): void {
        parent::setUp();
        $this->repo = $this->getEntityManager()->getRepository(User::class);
    }

    /** @test */
    public function createUser(): void 
    {
        $user = User::createNewFromArray(array(
            "username" => "user1",
            "firstName" => "FirstName11",
            "lastName" => "LastName11",
            "email" => "email11@domain.com"
        ));
        $created = $this->repo->save($user);
        $this->assertTrue($created,"user not created");
    }

    /** @test */
    public function getExistingUserByUsername(): void
    {
        $username = "testuser1";
        $testuser1 = $this->repo->getByUsername($username);
        $this->assertNotNull($testuser1,"existing user not fetched");
        $this->assertEquals($username,$testuser1->getUsername(),"fetched user with different username");
    }

    /** @test */
    public function getNotExistingUserByUsername(): void
    {
        $username = "nonexistinguser";
        $user = $this->repo->find($username);
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
        $this->repo->save($user);
    }

    /** @test */
    public function updateExistingUser(): void
    {
        $user = User::createNewFromArray(array(
            "userId" => 1,
            "username" => "testuser1_updated",
            "firstName" => "FirstName1",
            "lastName" => "LastName1",
            "email" => "email1@domain.com"
        ));
        $updated = $this->repo->save($user);
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
        $this->repo->save($user);
    }

    /** @test */
    public function searchUser(): void {
        $users = $this->repo->searchUser("test");
        $this->assertNotNull($users,"no result found");
        $this->assertEquals(3,count($users),"number of results does not match");
    }

    /** @test */
    public function removeExistingUser(): void
    {
        $userId = 1;
        $removed = $this->repo->removeUser($userId);
        $this->assertTrue($removed);
    }

    /** @test */
    public function removeNonExistingUser(): void
    {
        $userId = 25;
        $removed = $this->repo->removeUser($userId);
        $this->assertNotTrue($removed);
    }

    /** @test */
    public function countPostForUserHavingPosts(): void
    {
        $user = $this->repo->find(1);
        $this->assertNotNull($user->getMyPosts(), "no post loadded");
        $this->assertEquals(2,count($user->getMyPosts()),"required number of posts not loaded");
    }
}
