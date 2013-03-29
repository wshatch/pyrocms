<?php
/**
Because a good bit of 2.3 modules call ci(), we need to decompose the 
mock CI singleton into it's own object instead of just inheriting stuff from
ci_testcase.
*/
class CI
{
    protected $ci_config;
    protected $ci_instance;
    protected static $ci_test_instance;

    private $pyro_map = array(
       'config' => 'cfg',
       'lang' => 'lang',
       'loader' => 'load',
    );

    public static $APP;
	private $global_map = array(
		'benchmark'	=> 'bm',
		'config'	=> 'cfg',
		'hooks'		=> 'ext',
		'utf8'		=> 'uni',
		'router'	=> 'rtr',
		'output'	=> 'out',
		'security'	=> 'sec',
		'input'		=> 'in',
		'lang'		=> 'lang',
		'loader'	=> 'load',
		'model'		=> 'model'
	);


    public function __construct()
    {
        self::$APP = $this;
        $this->ci_config = array();
    }

    public function ci_set_config($key, $val = '')
    {
        if (is_array($key))
        {
            $this->ci_config = $key;
        }
        else
        {
            $this->ci_config[$key] = $val;
        }
 
    }

    public function &ci_core_class($name)
    {
        $name = strtolower($name);

        if (isset($this->global_map[$name]))
        {
            $class_name = ucfirst($name);
            $global_name = $this->global_map[$name];
        }
        elseif (in_array($name, $this->global_map))
        {
            $class_name = ucfirst(array_search($name, $this->global_map));
            $global_name = $name;
        }
        else
        {
            throw new Exception('Not a valid core class.');
        }
        if ( ! class_exists('MY_'.$class_name))
        {
            require_once BASEPATH.'/cms/core/'.$class_name.'.php';
        }

        $GLOBALS[strtoupper($global_name)] = 'MY_'.$class_name;
        return $GLOBALS[strtoupper($global_name)];

    }

    public function ci_instance($obj = FALSE)
    {
        if ( ! is_object($obj))
        {
            return $this->ci_instance;
        }
        $this->ci_instance = $obj;

    }

    public function get_config()
    {
        return $this->ci_config;
    }

    public function ci_instance_var($name, $obj=FALSE)
    {
        if ( ! is_object($obj))
        {
            return $this->ci_instance->$name;
        }

        $this->ci_instance->$name =& $obj;
    }

    public function helper($name)
    {
        //include the helper name.
    }
}
new CI;
