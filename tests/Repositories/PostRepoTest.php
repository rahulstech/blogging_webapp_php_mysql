<?php

namespace Rahulstech\Blogging\Tests\Repositories;

use DateTime;
use Rahulstech\Blogging\Entities\Post;
use Rahulstech\Blogging\Entities\User;
use Rahulstech\Blogging\Repositories\PostRepo;
use Rahulstech\Blogging\Tests\DatabaseTestCase;


class PostRepoTest extends DatabaseTestCase
{
    private PostRepo $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = $this->getEntityManager()->getRepository(Post::class);
    }

	/** @test */
    public function createPost(): void
    {
        $post = Post::createFromArray(array(
            "creator" => $this->getEntityManager()->getReference(User::class,1),
            "createdOn" => new DateTime("now"),
            "title" => "new post by testuser1",
            "shortDescription" => "this is another new post by testuser1",
            "textContent" => "this is completely new post by testuser1\nthis is completely new post by testuser1\nthis is completely new post by testuser1"
        ));
        $created = $this->repo->save($post);
        $this->assertTrue($created);
    }

    /** @test */
    public function removeAllPostForCreatorHavingPosts(): void 
    {
        $removed = $this->repo->removeAllPostsOfCreator(
                    $this->getEntityManager()->getReference(User::class,1));
        $this->assertTrue($removed);
    }

    /** @test */
    public function removeAllPostForCreatorHavingNoPosts(): void 
    {
        $removed = $this->repo->removeAllPostsOfCreator(
                    $this->getEntityManager()->getReference(User::class,4));
        $this->assertTrue($removed);
    }

    /** @test */
    public function removeAllPostForNotExistingCreator(): void 
    {
        $removed = $this->repo->removeAllPostsOfCreator(
                    User::createNewFromArray(array("userId"=>77)));
        $this->assertNotTrue($removed);
    }
}
