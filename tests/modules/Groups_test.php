<?php
defined('PROJECT_BASE') OR define('PROJECT_BASE', realpath($dir.'/../../').'/');
include_once PROJECT_BASE.'vendor/autoload.php';

use Pyro\Module\Navigation\Model\Group as Group;
use Pyro\Module\Navigation\Model\Link as Link;

class Group_test extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        $this->createMockData();
    }
    public function testRelationWithLinks()
    {
        $this->assertTrue(true, true);
    }

    public function testGetGroupOptions()
    {
    }
    private function createMockData()
    {
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
    }
}
