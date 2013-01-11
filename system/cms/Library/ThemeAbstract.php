<?php

namespace Library;

/**
 * Theme Interface
 *
 * This class should be extended to allow for theme management.
 *
 * @author		Stephen Cozart
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Libraries
 * @abstract
 */
abstract class ThemeAbstract
{	
	/**
	 * @var theme name
	 */
	public $name = '????';

	/**
	 * @var author name
	 */
	public $author = 'John Doe';

	/**
	 * @var authors website
	 */
	public $author_website;

	/**
	 * @var theme website
	 */
	public $website;

	/**
	 * @var theme description
	 */
	public $description;

	/**
	 * @var The version of the theme.
	 */
	public $version = '0.0.1';
	
	/**
	 * @var Front-end or back-end.
	 */
	public $type;
	
	/**
	 * @var Designer defined options.
	 */
	public $options;
}

/* End of file ThemeInterface.php */