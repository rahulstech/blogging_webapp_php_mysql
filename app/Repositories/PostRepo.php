<?php

namespace Rahulstech\Blogging\Repositories;

use Doctrine\ORM\EntityRepository;
use Rahulstech\Blogging\Entities\Post;
use Rahulstech\Blogging\Entities\User;

class PostRepo extends EntityRepository
{
    public function save(Post $post): bool
    {
        $em = $this->getEntityManager();
        $em->persist($post);
        $em->flush();
        return $post->getPostId() > 0;
    }

    public function removePost(int $postId): bool
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->delete(Post::class, "p")
            ->where("p.postId = :postId")
            ->setParameter("postId", $postId)
            ->getQuery();
        return $query->execute() == 1;
    }

    public function removeAllPostsOfCreator(User $creator): bool
    {
        if (!$this->getEntityManager()->contains($creator)) return false;
        $no_of_posts = $this->getEntityManager()->createQueryBuilder()
            ->select("COUNT(p.postId) AS noOfPosts")
            ->from(Post::class, "p")
            ->where("p.creator = :creator")
            ->setParameter("creator", $creator)
            ->getQuery()
            ->getScalarResult()[0]["noOfPosts"];
        if ($no_of_posts > 0) {
            $query = $this->getEntityManager()->createQueryBuilder()
                ->delete(Post::class, "p")
                ->where("p.creator = :creator")
                ->setParameter("creator", $creator)
                ->getQuery();
            return $no_of_posts == $query->execute();
        }
        return true;
    }
}
