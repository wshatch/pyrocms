<?php

class Lang_test extends CI_TestCase {

	protected $lang;
    private $ci_path;
	public function set_up()
	{
        $this->ci_path = BASEPATH.'codeigniter/';
		$loader_cls = $this->ci_core_class('load');
		$this->ci_instance_var('load', new $loader_cls);

		$cls = $this->ci_core_class('lang');
		$this->lang = new $cls;
	}

	// --------------------------------------------------------------------

	public function test_load()
	{
		$this->assertTrue($this->lang->load('profiler', 'english', FALSE,TRUE,$this->ci_path));
		$this->assertEquals('URI STRING', $this->lang->line('profiler_uri_string'));
	}

	// --------------------------------------------------------------------

	public function test_load_with_unspecified_language()
	{
		$this->assertTrue($this->lang->load('profiler', '', False,True,$this->ci_path));
		$this->assertEquals('URI STRING', $this->lang->line('profiler_uri_string'));
	}

}
