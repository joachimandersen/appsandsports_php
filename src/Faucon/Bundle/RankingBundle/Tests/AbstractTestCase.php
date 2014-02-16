<?php

namespace Faucon\Bundle\RankingBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;

use Faucon\Bundle\RankingBundle\DataFixtures\ORM\LoadUsers;
use Faucon\Bundle\RankingBundle\DataFixtures\ORM\LoadClubs;
use Faucon\Bundle\RankingBundle\DataFixtures\ORM\LoadCategories;
use Faucon\Bundle\RankingBundle\DataFixtures\ORM\LoadRankings;

abstract class AbstractTestCase extends WebTestCase
{
    protected $entitymanager;
        
    public function setUp()
    {
        parent::setUp();
        static::$kernel = $this->createKernel();
        static::$kernel->boot();
        $this->entitymanager = $this->getEntityManager();
        $this->generateSchema();
        $loader = new Loader();
        $loader->addFixture(new LoadUsers(static::$kernel->getContainer()));
        $loader->addFixture(new LoadClubs(static::$kernel->getContainer()));
        $loader->addFixture(new LoadCategories(static::$kernel->getContainer()));
        $loader->addFixture(new LoadRankings(static::$kernel->getContainer()));
        $this->loadFixtures($loader);
    }
    
    public function tearDown()
    {
        // Shutdown the kernel.
        static::$kernel->shutdown();
        //$this->dropDatabase();
        parent::tearDown();
    }
    
    protected function generateSchema()
    {
        // Get the metadatas of the application to create the schema.
        $metadatas = $this->getMetadatas();
 
        if ( ! empty($metadatas)) {
            // Create SchemaTool
            $tool = new SchemaTool($this->entitymanager);
            $tool->dropDatabase();
            $this->entitymanager->getConnection()->exec('PRAGMA foreign_keys = true;');
            $tool->createSchema($metadatas);
        }
        else {
            throw new Doctrine\DBAL\Schema\SchemaException('No Metadata Classes to process.');
        }
    }
    
    protected function dropDatabase()
    {
        // Get the metadatas of the application to create the schema.
        $metadatas = $this->getMetadatas();
 
        if ( ! empty($metadatas)) {
            // Create SchemaTool
            $tool = new SchemaTool($this->entitymanager);
            $tool->dropDatabase();
        }
        else {
            throw new Doctrine\DBAL\Schema\SchemaException('No Metadata Classes to process.');
        }
    }
    
    protected function getMetadatas()
    {
        return $this->entitymanager->getMetadataFactory()->getAllMetadata();
    }
    
    protected function getEntityManager()
    {
        return static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public function loadFixtures($loader)
    {
        //$em = static::$kernel->getContainer()->get()
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entitymanager, $purger);
        $executor->execute($loader->getFixtures());
    }
}

?>
