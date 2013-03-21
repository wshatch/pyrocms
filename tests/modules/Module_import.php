<?php
/**Module class stoled from the installer. Used to install the helper DB for unit testing.
*/
//Define the needed paths here since we aren't importing index.php
define('PYROPATH','../../system/cms/'); 
define('DEFAULT_LANG', '');
define('DEFAULT_EMAIL', '');
include PYROPATH.'core/MY_Model.php';

// All modules talk to the Module class, best get that!
include PYROPATH.'libraries/Module.php';

class Module_import
{
	public function __construct(array $params)
	{
		$this->pdb = $params['pdb'];
	}

	/**
	 * Installs a module
	 *
	 * @param string $slug The module slug
	 * @param bool   $is_core
	 *
	 * @return bool
	 */
	public function install($slug, $is_core = false)
	{
		if ( ! ($details_class = $this->_spawn_class($slug, $is_core))) {
			exit("The module $slug is missing a details.php");
		}

		// Get some basic info
		$module = $details_class->info();

		// Now lets set some details ourselves
		$module['version'] = $details_class->version;
		$module['is_core'] = $is_core;
		$module['enabled'] = true;
		$module['installed'] = true;
		$module['slug'] = $slug;

		// set the site_ref and upload_path for third-party devs
		$details_class->site_ref = 'default';
		$details_class->upload_path = 'uploads/default/';

		// Run the install method to get it into the database
		// try
		// {
			$details_class->install();
		// }
		// catch (Exception $e)
		// {
		// 	// TODO Do something useful
		// 	exit('HEY '.$e->getMessage()." in ".$e->getFile()."<br />");

		// 	return false;
		// }

		// Looks like it installed ok, add a record
		return $this->add($module);
	}

	/**
	 * Add
	 *
	 * Insert the database record for a single module
	 *
	 * @param     array     Array of module informaiton.
	 * @return    boolean
	 */
	public function add($module)
	{
		return $this->pdb
			->table('modules')
			->insert(array(
				'name' => serialize($module['name']),
				'slug' => $module['slug'],
				'version' => $module['version'],
				'description' => serialize($module['description']),
				'skip_xss' => ! empty($module['skip_xss']),
				'is_frontend' => ! empty($module['frontend']),
				'is_backend' => ! empty($module['backend']),
				'menu' => ! empty($module['menu']) ? $module['menu'] : false,
				'enabled' => (bool) $module['enabled'],
				'installed' => (bool) $module['installed'],
				'is_core' => (bool) $module['is_core']
			)
		);
	}

	/**
	 * Import All
	 *
	 * Create settings and streams core, and run the install() method for all modules
	 *
	 * @return    boolean
	 */
	public function import_all()
	{
		// Install settings and streams core first. Other modules may need them.
		$this->install('settings', true);
		//ci()->load->library('settings/settings');
		$this->install('streams_core', true);

		// Are there any modules to install on this path?
		if ($modules = glob(PYROPATH.'modules/*', GLOB_ONLYDIR)) {
			// Loop through modules
			foreach ($modules as $module_name) {
				$slug = basename($module_name);

				if ($slug == 'streams_core' or $slug == 'settings') {
					continue;
				}

				// invalid details class?
				if ( ! $details_class = $this->_spawn_class($slug, true)) {
					continue;
				}
				$this->install($slug, true);
			}
		}

		// After modules are imported we need to modify the settings table
		// This allows regular admins to upload addons on the first install but not on multi
		$this->pdb
			->table('settings')
			->where('slug', '=', 'addons_upload')
			->update(array('value' => true));

		return true;
	}

    /**
    * Installs the core table to the testing database.
    */
    public function install_core()
    {
		// Include migration config to know which migration to start from
		require PYROPATH.'config/migration.php';

		$schema = $this->pdb->getSchemaBuilder();

		// Remove any tables not installed by a module
		$schema->dropIfExists('core_users');
		$schema->dropIfExists('core_settings');
		$schema->dropIfExists('core_sites');
		$schema->dropIfExists('sess_table_name');
		$schema->dropIfExists('modules');
		$schema->dropIfExists('migrations');
		$schema->dropIfExists('settings');
		$schema->dropIfExists('users');
		$schema->dropIfExists('profiles');

		// Create core_settings first
		$schema->create('core_settings', function($table) {
		    $table->string('slug', 30);
		    $table->text('default')->nullable();
		    $table->text('value')->nullable();

		    $table->unique('slug');
		    $table->index('slug');
		});

		// Populate core settings
		$this->pdb->table('core_settings')->insert(array(
			array(
				'slug'    => 'date_format',
				'default' => 'g:ia -- m/d/y',
			),
			array(
				'slug'    => 'lang_direction',
				'default' => 'ltr',
			),
			array(
				'slug'    => 'status_message',
				'default' => 'This site has been disabled by a super-administrator.',
			),
		));

		// Core Sites
		$schema->create('core_sites', function($table) {
		    $table->increments('id');
		    $table->string('name', 100);
		    $table->string('ref', 20);
		    $table->string('domain', 100);
		    $table->boolean('active')->default(1);
		    $table->integer('created_on');
		    $table->integer('updated_on')->nullable();

		    $table->unique('ref');
		    $table->unique('domain');
		    $table->index('ref');
		    $table->index('domain');
		});

		// User Table is used for site users and core users
		$user_table = function($table) {
		    $table->increments('id');
		    $table->string('username', 20);
		    $table->string('email', 60);
		    $table->string('password', 40);
		    $table->string('salt', 6);
		    $table->integer('group_id')->nullable();
		    $table->string('ip_address');
		    $table->boolean('active')->default(1);
		    $table->string('activation_code', 40)->nullable();
		    $table->string('forgotten_password_code', 40)->nullable();
		    $table->string('remember_code', 40)->nullable();
		    $table->integer('created_on');
		    $table->integer('updated_on')->nullable();
		    $table->integer('last_login')->nullable();

		    $table->unique('email');
		    $table->unique('username');
		    $table->index('email');
		    $table->index('username');
		};

		// Create User tables
		$schema->create('core_users', $user_table);
		$schema->create('users', $user_table);

		$schema->create('sess_table_name', function($table) {
		    $table->string('session_id', 40)->default(0);
		    $table->string('ip_address', 16)->default(0);
		    $table->string('user_agent', 120);
		    $table->integer('last_activity')->default(0);
		    $table->text('user_data');

		    $table->index('last_activity', 'last_activity_idx');
		});


		// Profiles
		$schema->create('profiles', function($table) {
		    $table->increments('id');
		    $table->integer('user_id');
		    $table->string('display_name', 50); // TODO Revise these lengths
		    $table->string('first_name', 50);
		    $table->string('last_name', 50)->nullable();
		    $table->string('company', 100)->nullable();
		    $table->string('lang', 2)->default('en');
		    $table->text('bio')->nullable();
		    $table->integer('dob')->nullable();
		    $table->enum('gender', array('m', 'f', ''))->default('');
		    $table->string('phone', 20)->nullable();
		    $table->string('website', 255)->nullable();
		    $table->integer('updated_on')->nullable();

		    $table->index('user_id');
		});

		// Migrations
		$schema->create('migrations', function($table) {
		    $table->integer('version');
		});

		// Insert current latest migration
		$this->pdb->table('migrations')->insert(array(
			'version' => $config['migration_version'],
		));

		// Modules
		// TODO make migration to remove "type" field from here
		$schema->create('modules', function($table) {
		    $table->increments('id');
		    $table->text('name');
		    $table->string('slug', 50);
		    $table->string('version', 20);
		    $table->text('description');
		    $table->boolean('skip_xss');
		    $table->boolean('is_frontend');
		    $table->boolean('is_backend');
		    $table->string('menu', 20);
		    $table->boolean('enabled');
		    $table->boolean('installed');
		    $table->boolean('is_core');
		    $table->integer('updated_on')->nullable();

		    $table->unique('slug');
		    $table->index('enabled');
		    $table->index('is_frontend');
		    $table->index('is_backend');
		});

		$schema->create('settings', function($table) {
		    $table->string('slug', 30);
		    $table->string('title', 100);
		    $table->text('description');
		    $table->enum('type', array('text','textarea','password','select','select-multiple','radio','checkbox'));
		    $table->text('default')->nullable();
		    $table->text('value')->nullable();
		    $table->string('options', 255)->nullable();
		    $table->boolean('is_required')->default(false);
		    $table->boolean('is_gui')->default(true);
		    $table->string('module', 50)->nullable();
		    $table->integer('order')->default(0);

		    $table->unique('slug');
		    $table->index('slug');
		});

    }

	/**
	 * Spawn Class
	 *
	 * Checks to see if a details.php exists and returns a class
	 *
	 * @param string $slug    The folder name of the module
	 * @param bool   $is_core
	 *
	 * @return    Module
	 */
	private function _spawn_class($slug, $is_core = false)
	{
		$path = PYROPATH;

		// Before we can install anything we need to know some details about the module<<<<<<< HEAD
		$details_file = "{$path}modules/{$slug}/details.php";

		// If it didn't exist as a core module or an addon then check shared_addons
		if ( ! is_file($details_file)) {
			$details_file = "{SHARED_ADDONPATH}modules/{$slug}/details.php";

			if ( ! is_file($details_file)) {
				return false;
			}
		}

		// Sweet, include the file
		include_once $details_file;

		// Now call the details class
		$class = 'Module_'.ucfirst(strtolower($slug));

		// Now we need to talk to it
		return class_exists($class) ? new $class($this->pdb) : false;
	}


}
