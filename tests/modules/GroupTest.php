<?php
defined('PROJECT_BASE') OR define('PROJECT_BASE', realpath($dir.'/../../').'/');
include_once PROJECT_BASE.'vendor/autoload.php';

use Pyro\Module\Navigation\Model\Group as Group;
use Pyro\Module\Navigation\Model\Link as Link;
Use Iluminate\Database\SQLiteConnection;
use Capsule\Database\Connection;


/**
 * @backupGlobals disabled
 */
class GroupTest extends PHPUnit_Framework_TestCase 
{
    public function __construct()
    {
        $conn = Capsule\Database\Connection::make(
            'default',
            array(
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => ''
            )
        ); 
        $conn->setFetchMode(PDO::FETCH_OBJ);
        $resolver = Capsule\Database\Connection::getResolver();
        $resolver->addConnection('default', $conn);
        $resolver->setDefaultConnection('default');
        $this->createMockData();
    }
    public function testRelationWithLinks()
    {
        $foo = true;
        $this->assertTrue($foo);
    }

    public function testGetGroupOptions()
    {
    }
    private function createMockData()
    {
        /*
        Group::create(array(
            'title'  => 'Header',
            'abbrev' => 'header'
        ))->save();
        Group::create(array(
            'title'  => 'Sidebar',
            'abbrev' => 'sidebar'
        ))->save();
        Group::create(array(
            'title'  => 'Footer',
            'abbrev' => 'footer'
        ))->save();
        */
    }
}
