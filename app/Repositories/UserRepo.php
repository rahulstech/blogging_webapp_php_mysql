<?php

namespace Rahulstech\Blogging\Repositories;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Rahulstech\Blogging\Entities\User;

class UserRepo extends EntityRepository
{
    public function save(User $user): bool {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user->getUserId() > 0;
    }

    public function getByUsername(string $username): ?User {
        $dql = "SELECT u FROM ".User::class." u WHERE u.username = :username";
        $query = $this->createQueryBuilder("u")
                    ->leftJoin("u.myPosts","p")
                    ->where("u.username = :username")
                    ->orderBy("p.createdOn","DESC")
                    ->setParameter("username",$username)
                    ->getQuery();
        return $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    public function searchUser(string $searchkey): array {
        $query = $this->createQueryBuilder("u")
                    ->where("u.username LIKE :searchkey")
                    ->orWhere("u.firstName LIKE :searchkey")
                    ->orWhere("u.lastName LIKE :searchkey")
                    ->setParameter("searchkey","%".$searchkey."%")
                    ->getQuery();
        return $query->getResult();
    }

    public function removeUser(int|string $userId): bool {
        $query = $this->getEntityManager()
                    ->createQueryBuilder()
                    ->delete(User::class,"u")
                    ->where("u.userId = :userId")
                    ->setParameter("userId",$userId)
                    ->getQuery();
        return 1 == $query->execute();
    }
}
