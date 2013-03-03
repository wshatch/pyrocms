<?php

/**
 * Mock library to add testing features to Session driver library
 */
class Mock_Libraries_Session extends CI_Session {
    /**
    * We have to copy/paste a lot of the CI_session constructor logic
    * since sessions are returned if called from the cli.
    * TODO: figure out how to mock a real request and if there are any consequences
    * for doing so.
    */
    public function __construct(array $params = array())
    {
		$CI =& get_instance();
		// Get valid drivers list
		$this->valid_drivers = array(
			'native',
			'cookie'
		);
		$key = 'sess_valid_drivers';
		$drivers = isset($params[$key]) ? $params[$key] : $CI->config->item($key);
		if ($drivers)
		{
			// Add driver names to valid list
			foreach ((array) $drivers as $driver)
			{
				if ( ! in_array(strtolower($driver), array_map('strtolower', $this->valid_drivers)))
				{
					$this->valid_drivers[] = $driver;
				}
			}
		}

		// Get driver to load
		$key = 'sess_driver';
		$driver = isset($params[$key]) ? $params[$key] : $CI->config->item($key);
		if ( ! $driver)
		{
			$driver = 'cookie';
		}

		if ( ! in_array(strtolower($driver), array_map('strtolower', $this->valid_drivers)))
		{
			$this->valid_drivers[] = $driver;
		}

		// Save a copy of parameters in case drivers need access
		$this->params = $params;

		// Load driver and get array reference
		$this->load_driver($driver);

		// Delete 'old' flashdata (from last request)
		$this->_flashdata_sweep();

		// Mark all new flashdata as old (data will be deleted before next request)
		$this->_flashdata_mark();

		// Delete expired tempdata
		$this->_tempdata_sweep();

		log_message('debug', 'CI_Session routines successfully run');

    }
	/**
	 * Simulate new page load
	 */
	public function reload()
	{
		$this->_flashdata_sweep();
		$this->_flashdata_mark();
		$this->_tempdata_sweep();
	}

}

/**
 * Mock cookie driver to overload cookie setting
 */
class Mock_Libraries_Session_cookie extends CI_Session_cookie {
	/**
	 * Overload _setcookie to manage $_COOKIE values, since actual cookies can't be set in unit testing
	 */
	protected function _setcookie($name, $value = '', $expire = 0, $path = '', $domain = '', $secure = false,
	$httponly = false)
	{
		if (empty($value) || $expire <= time()) {
			// Clear cookie
			unset($_COOKIE[$name]);
		}
		else {
			// Set cookie
			$_COOKIE[$name] = $value;
		}
	}
}

/**
 * Mock native driver (just for consistency in loading)
 */
class Mock_Libraries_Session_native extends CI_Session_native { }

