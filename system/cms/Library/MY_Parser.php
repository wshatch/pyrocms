<?php

/**
 * CodeIgniter Dwoo Parser Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @license	 http://philsturgeon.co.uk/code/dbad-license
 * @link		http://philsturgeon.co.uk/code/codeigniter-dwoo
 */
class MY_Parser extends CI_Parser {

	public function __construct($config = array())
	{
		$this->plugins = new \Library\Plugins;
	}
	/**
	 *  Parse a view file
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse($template, $data = array(), $return = false, $is_include = false, $streams_parse = array())
	{
		$string = ci()->load->view($template, $data, true);

		return $this->_parse($string, $data, $return, $is_include, $streams_parse);
	}

	/**
	 *  String parse
	 *
	 * Parses pseudo-variables contained in the string content,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse_string($string, $data = array(), $return = false, $is_include = false, $streams_parse = array())
	{
		return $this->_parse($string, $data, $return, $is_include, $streams_parse);
	}

	/**
	 *  Parse
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	protected function _parse($string, $data, $return = false, $is_include = false, $streams_parse = array())
	{
		// Start benchmark
		ci()->benchmark->mark('parse_start');

		// Convert from object to array
		is_array($data) or $data = (array) $data;

		$data = array_merge($data, ci()->load->get_vars());

		if ($streams_parse and isset($streams_parse['stream']) and isset($streams_parse['namespace']))
		{
			ci()->load->driver('Streams');
			$parsed = ci()->streams->parse->parse_tag_content($string, $data, $streams_parse['stream'], $streams_parse['namespace']);
		}
		else
		{
			$parser = new Lex\Parser();
			$parser->scopeGlue(':');
			$parser->cumulativeNoparse(true);
			$parsed = $parser->parse($string, $data, array($this, 'parser_callback'));
		}
		
		// Finish benchmark
		ci()->benchmark->mark('parse_end');
		
		// Return results or not ?
		if ( ! $return)
		{
			ci()->output->append_output($parsed);
			return;
		}

		return $parsed;
	}

	/**
	 * Callback from template parser
	 *
	 * @param	array
	 * @return	 mixed
	 */
	public function parser_callback($plugin, $attributes, $content)
	{
		$return_data = $this->plugins->locate($plugin, $attributes, $content);

		if (is_array($return_data) && $return_data)
		{
			if ( ! $this->isMulti($return_data))
			{
				$return_data = $this->makeMulti($return_data);
			}

			# TODO What was this doing other than throw warnings in 2.0? Phil
			// $content = $data['content']; 
			$parsed_return = '';

			$parser = new Lex\Parser();
			$parser->scopeGlue(':');
			
			foreach ($return_data as $result)
			{
				// TODO Why is this commented out? Phil
				// if ($data['skip_content'])
				// {
				// 	$simpletags->set_skip_content($data['skip_content']);
				// }

				$parsed_return .= $parser->parse($content, $result, array($this, 'parser_callback'));
			}

			unset($parser);

			$return_data = $parsed_return;
		}

		return $return_data;
	}

	/**
	 * Ensure we have a multi array
	 *
	 * @param	array
	 * @return	 int
	 */
	private function isMulti($array)
	{
		return (count($array) != count($array, 1));
	}

	/**
	 * Forces a standard array in multidimensional.
	 *
	 * @param	array
	 * @param	int		Used for recursion
	 * @return	array	The multi array
	 */
	private function makeMulti($flat, $i=0)
	{
		$multi = array();
		$return = array();
		foreach ($flat as $item => $value)
		{
			is_object($value) and $value = (array) $value;
			$return[$i][$item] = $value;
		}
		return $return;
	}
}

/* End of file MY_Parser.php */