<?php namespace Library;

/**
 * Central library for Plugin logic
 *
 * @author   PyroCMS Dev Team
 * @package  PyroCMS\Core\Libraries
 */
class Plugins
{
	private $loaded = array();

	public function __construct()
	{
		ci()->load->helper('plugin');
	}

	public function locate($plugin, $attributes, $content)
	{
		if (strpos($plugin, ':') === false)
		{
			return false;
		}

		// Setup our paths from the data array
		list($class, $method) = array_map('strtolower', explode(':', $plugin));

		$class = ucfirst($class);
		$method = camel_case($method);

		foreach (array(APPPATH, ADDONPATH, SHARED_ADDONPATH) as $directory) {
			if (file_exists($path = $directory.'Plugin/'.$class.'.php')) {
				return $this->process($path, $class, $method, $attributes, $content);
			
			} elseif (defined('ADMIN_THEME') and file_exists($path = APPPATH.'Theme/'.ADMIN_THEME.'/Plugin/'.$class.'.php')) {
				return $this->process($path, $class, $method, $attributes, $content);
			}

			// Maybe it's a module
			if (module_exists($class)) {
				if (file_exists($path = $directory.'Module/'.$class.'/Plugin.php')) {
					$dirname = dirname($path).'/';

					// Set the module as a package so I can load stuff
					ci()->load->add_package_path($dirname);

					$response = $this->process($path, $class, $method, $attributes, $content);

					ci()->load->remove_package_path($dirname);

					return $response;
				}
			}
		}

		log_message('debug', 'Unable to load: '.$class);

		return false;
	}

	/**
	 * Process
	 *
	 * Just process the class
	 *
	 * @todo Document this better.
	 *
	 * @param string $path
	 * @param string $class
	 * @param string $method
	 * @param array $attributes
	 * @param array $content
	 *
	 * @return bool|mixed
	 */
	private function process($path, $class, $method, $attributes, $content)
	{
		$class = 'Module\\'.ucfirst(strtolower($class)).'\\Plugin';

		if ( ! isset($this->loaded[$class])) {
			include $path;
			$this->loaded[$class] = true;
		}

		if ( ! class_exists($class)) {
			throw new Exception('Plugin "' . $class_name . '" does not exist.');
		}

		$object = new $class;
		$object->set_data($content, $attributes);

		if ( ! is_callable(array($object, $method))) {
			// But does a property exist by that name?
			if (property_exists($object, $method)) {
				return true;
			}

			throw new Exception('Plugin method "'.$method.'" does not exist on class "'.$class.'".');

			return false;
		}

		return call_user_func(array($object, $method));
	}
}
