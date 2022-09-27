<?php

namespace Rahulstech\Blogging;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Rahulstech\Blogging\Entities\Post;
use Rahulstech\Blogging\Entities\User;
use Rahulstech\Blogging\Repositories\PostRepo;
use Rahulstech\Blogging\Repositories\UserRepo;

class DatabaseBootstrap
{

    private static ?EntityManager $em = null;

    private function __construct()
    {}

    public static function setup() : void
    {
        if (is_null(DatabaseBootstrap::$em))
        {
            $paths = [dirname(__DIR__) . '\\app'];
            $isDevMode = true;
            $conn = array(
                'driver' => DB_DRIVER,
                'user' => DB_USER,
                'password' => DB_PASS,
                'host' => DB_HOST,
                'port' => DB_PORT,
                'dbname' => DB_NAME
            );
            $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode,null,null,false);
            DatabaseBootstrap::$em = EntityManager::create($conn, $config);
        }
    }

    public static function setup_test(): void 
    {
        if (is_null(DatabaseBootstrap::$em))
        {
            $paths = [dirname(__DIR__) . '\\app'];
            $isDevMode = true;
            $conn = array(
                'driver' => TEST_DB_DRIVER,
                'memory' => true
            );
            $config = Setup::createAnnotationMetadataConfiguration($paths,$isDevMode,null,null,false);
            DatabaseBootstrap::$em = EntityManager::create($conn, $config);
        }
    }

    public static function getUserRepo(): ?UserRepo
    {
        return DatabaseBootstrap::getRepository(User::class);
    }

    public static function getPostRepo(): ?PostRepo
    {
        return DatabaseBootstrap::getRepository(Post::class);
    }

    public static function getRepository(string $entityclass): ?EntityRepository
    {
        if (!is_null(DatabaseBootstrap::$em)) return DatabaseBootstrap::$em->getRepository($entityclass);
        return null;
    }

    public static function getEntityManager(): ?EntityManager
    {
        return DatabaseBootstrap::$em;
    }

    public static function close(): void
    {
        if (!is_null(DatabaseBootstrap::$em))
        {
            DatabaseBootstrap::$em->close();
            DatabaseBootstrap::$em = null;
        }
    }
}
