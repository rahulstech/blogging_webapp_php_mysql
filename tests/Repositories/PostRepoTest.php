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

    /** @test */
    public function getLastestPosts(): void
    {
        $posts = $this->repo->getLatestPosts();
        $this->assertNotEmpty($posts,"no posts fetched");
        $this->assertEquals(new DateTime("2022-10-04 13:30:00"),$posts[0]->getCreatedOn(),"fetched posts not sorted properly");
    }

    /** @test */
    public function getPostsTitleContains_KeyExists(): void 
    {
        $phrase = "testuser3";
        $posts = $this->repo->getPostsTitleContains($phrase);
        $this->assertNotNull($posts,"no result returned");
        $this->assertEquals(1,count($posts),"expected number of results not fetched");
        $this->assertEquals(5,$posts[0]->getPostId(),"wrong post fetched");
    }

    /** @test */
    public function getPostsTitleContains_KeyNotExists(): void 
    {
        $phrase = "non existing";
        $posts = $this->repo->getPostsTitleContains($phrase);
        $this->assertEmpty($posts,"no result expected");
    }
}
