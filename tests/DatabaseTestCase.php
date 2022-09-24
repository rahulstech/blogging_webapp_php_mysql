<?php

namespace Rahulstech\Blogging\Tests;

use PHPUnit\Framework\TestCase;
use Rahulstech\Blogging\DatabaseBootstrap;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;

class DatabaseTestCase extends TestCase
{
    protected function setUp(): void 
    {
        DatabaseBootstrap::setup_test();
        $em = DatabaseBootstrap::getEntityManager();
        (new SchemaTool($em))->updateSchema(
            $em->getMetadataFactory()->getAllMetadata()
        );

        $this->populateTestData($em);
    }

    protected function populateTestData(EntityManager $em): void {
        $path = dirname(__DIR__)."/test-data/blogging-test-data.sql";
        $sql = file_get_contents($path);
        $em->getConnection()->executeStatement($sql);
    }

    protected function getEntityManager(): ?EntityManager 
    {
        return DatabaseBootstrap::getEntityManager();
    }

    protected function tearDown(): void {
        DatabaseBootstrap::close();
    }
}
