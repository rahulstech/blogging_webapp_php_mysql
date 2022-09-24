<?php

namespace Rahulstech\Blogging;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class DatabaseBootstrap
{

    private static ?EntityManager $em = null;

    private function __construct()
    {}

    public static function setup() : void
    {
        $paths = [dirname(__DIR__) . '\\app'];
        $isDevMode = true;
        $conn = array(
            'driver' => 'mysqli',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'port' => '3306',
            'dbname' => "blogging"
        );
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode,null,null,false);
        DatabaseBootstrap::$em = EntityManager::create($conn, $config);
    }

    public static function setup_test(): void 
    {
        $paths = [dirname(__DIR__) . '\\app'];
        $isDevMode = true;
        $conn = array(
            'driver' => 'pdo_sqlite',
            'memory' => true
        );
        $config = Setup::createAnnotationMetadataConfiguration($paths,$isDevMode,null,null,false);
        DatabaseBootstrap::$em = EntityManager::create($conn, $config);
    }

    public static function getEntityManager(): ?EntityManager
    {
        return DatabaseBootstrap::$em;
    }


    public static function close(): void
    {
        if (is_null(DatabaseBootstrap::$em))
        {
            DatabaseBootstrap::$em->close();
            DatabaseBootstrap::$em = null;
        }
    }
}
