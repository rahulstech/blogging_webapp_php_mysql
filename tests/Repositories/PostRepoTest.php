<?php

namespace Rahulstech\Blogging\Tests\Repositories;

use DateTime;
use Rahulstech\Blogging\Entities\Post;
use Rahulstech\Blogging\Entities\User;
use Rahulstech\Blogging\Repositories\PostRepo;
use Rahulstech\Blogging\Tests\DatabaseTestCase;


class PostRepoTest extends DatabaseTestCase
{
    private PostRepo $postRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->postRepo = $this->getEntityManager()->getRepository(Post::class);
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
        $created = $this->postRepo->save($post);
        $this->assertTrue($created);
    }

    /** @test */
    public function removeAllPostForCreatorHavingPosts(): void 
    {
        $removed = $this->postRepo->removeAllPostsOfCreator(
                    $this->getEntityManager()->getReference(User::class,1));
        $this->assertTrue($removed);
    }

    /** @test */
    public function removeAllPostForCreatorHavingNoPosts(): void 
    {
        $removed = $this->postRepo->removeAllPostsOfCreator(
                    $this->getEntityManager()->getReference(User::class,4));
        $this->assertTrue($removed);
    }

    /** @test */
    public function removeAllPostForNotExistingCreator(): void 
    {
        $removed = $this->postRepo->removeAllPostsOfCreator(
                    User::createNewFromArray(array("userId"=>77)));
        $this->assertNotTrue($removed);
    }

    /** @test */
    public function getLastestPosts(): void
    {
        $posts = $this->postRepo->getLatestPosts();
        $this->assertNotEmpty($posts,"no posts fetched");
        $this->assertEquals(new DateTime("2022-10-04 13:30:00"),$posts[0]->getCreatedOn(),"fetched posts not sorted properly");
    }

    /** @test */
    public function getCreatorPostsTitleContains(): void 
    {
        $creator = $this->getEntityManager()->getReference(User::class,1);
        $phrase = "2";
        $posts = $this->postRepo->getCreatorPostsTitleContains($creator,$phrase);
        $this->assertNotEmpty($posts,"fetched empty result");
        $this->assertEquals(2,count($posts),"fetched worng no of results");
        $this->assertEquals(2,$posts[0]->getPostId(),"fetched worong results");
    }

    /** @test */
    public function getCreatorPostsTitleContains_Pagination(): void 
    {
        $creator = $this->getEntityManager()->getReference(User::class,1);
        $phrase = "2";
        $posts = $this->postRepo->getCreatorPostsTitleContains($creator,$phrase,DB_MAX_RESULT,1);
        $this->assertNotEmpty($posts,"fetched empty result");
        $this->assertEquals(1,count($posts),"fetched worng no of results");
        $this->assertEquals(6,$posts[0]->getPostId(),"fetched worong results");
    }

    /** @test */
    public function getLatestPostsTitleContains(): void 
    {
        $phrase = "testuser1";
        $posts = $this->postRepo->getLatestPostsTitleContains($phrase);
        $this->assertNotEmpty($posts,"fetched empty result");
        $this->assertEquals(3,count($posts),"fetched worng no of results");
        $this->assertEquals(2,$posts[0]->getPostId(),"fetched worong results");
    }
}
