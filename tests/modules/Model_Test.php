<?php
defined('PROJECT_BASE') OR define('PROJECT_BASE', realpath($dir.'/../../').'/');
include_once PROJECT_BASE.'vendor/autoload.php';
include_once('Module_import.php');
include_once('../mocks/ci_testcase.php');
use Pyro\Module\Navigation\Model\Group as Group;
use Pyro\Module\Navigation\Model\Link as Link;
use Iluminate\Database\SQLiteConnection;
use Capsule\Database\Connection;
/**
 * @backupGlobals disabled
 */
class ModelTest extends CI_TestCase 
{
    protected $conn;
    public function __construct()
    {
        $this->conn = Capsule\Database\Connection::make(
            'default',
            array(
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => ''
            )
        ); 
        $this->conn->setFetchMode(PDO::FETCH_OBJ);
        $resolver = Capsule\Database\Connection::getResolver();
        $resolver->addConnection('default', $this->conn);
        $resolver->setDefaultConnection('default');
 
        $module_import = new Module_import(array('pdb'=>$this->conn));
        $module_import->install_core();
        $module_import->import_all();
    }
}
