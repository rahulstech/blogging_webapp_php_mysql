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

    /**
     * @return Post[]
     */
    public function getLatestPosts(): array
    {
        $query = $this->getEntityManager()->createQueryBuilder()
                    ->select("p")
                    ->from(Post::class,"p")
                    ->orderBy("p.createdOn","DESC")
                    ->setMaxResults(10)
                    ->getQuery();
        return $query->getResult();
    }

    /**
     * @return Post[]
     */
    public function getLatestPostsTitleContains(string $phrase): array 
    {
        $postIds = $this->getEntityManager()->createQueryBuilder()
                    ->select("p.postId")
                    ->from(Post::class,"p")
                    ->orderBy("p.createdOn","DESC")
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getSingleColumnResult();
        $query = $this->getEntityManager()->createQueryBuilder()
                    ->select("p")
                    ->from(Post::class,"p")
                    ->where("p.postId IN(:postIds)")
                    ->andWhere("p.title LIKE :title")
                    ->setParameter("postIds",$postIds)
                    ->setParameter("title","%$phrase%")
                    ->orderBy("p.createdOn","DESC")
                    ->getQuery();
        return $query->getResult();
    }

    /**
     * @return Post[]
     */
    public function getCreatorPostsTitleContains(User $creator, string $phrase, int $max = DB_MAX_RESULT, int $skip = 0): array
    {
        $query = $this->getEntityManager()->createQueryBuilder()
                    ->select("p")
                    ->from(Post::class,"p")
                    ->where("p.creator = :creator")
                    ->andWhere("p.title LIKE :title")
                    ->orderBy("p.createdOn","DESC")
                    ->setParameter("creator",$creator)
                    ->setParameter(":title","%$phrase%")
                    ->setFirstResult(max(abs($skip),0))
                    ->setMaxResults(min(abs($max),DB_MAX_RESULT))
                    ->getQuery();
        return $query->getResult();
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
