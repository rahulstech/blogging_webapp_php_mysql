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
            #'driver' => 'pdo_sqlite',
            #'path' => dirname(__DIR__) . '\\database\\blogging.sqlite',
            'driver' => 'mysqli',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'port' => '3306',
            'dbname' => 'blogging'
        );
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode,null,null,false);
        DatabaseBootstrap::$em = EntityManager::create($conn, $config);
    }

    public static function getEntityManager(): ?EntityManager
    {
        return DatabaseBootstrap::$em;
    }
}
