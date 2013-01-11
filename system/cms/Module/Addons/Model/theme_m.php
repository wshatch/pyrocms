<?php
/**
 * Theme model
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Themes\Model
 */
class Theme_m extends CI_Model
{
    /**
     * Available Themes
     *
     * @var array
     */
    public $_themes = null;

    /**
     * Get all available themes
     *
     * @return <array>
     */
    public function get_all()
    {
        foreach ($this->template->theme_locations() as $location)
        {
            if ( ! $themes = glob($location.'*', GLOB_ONLYDIR))
            {
                continue;
            }

            foreach ($themes as $theme_path)
            {
                $this->getDetails(dirname($theme_path).'/', basename($theme_path));
            }
        }

        ksort($this->_themes);

        return $this->_themes;
    }

    /**
     * Get a specific theme
     *
     * @param string $slug
     *
     * @return bool|object
     */
    public function get($slug)
    {
        foreach ($this->template->theme_locations() as $location) {
            if (is_dir($location.$slug)) {
                if (($theme = $this->getDetails($location, $slug))) {
                    return $theme;
                }
            }
        }

        return false;
    }


    /**
     * Get details about a theme
     *
     * @param $location
     * @param $slug
     *
     * @return bool|object
     */
    private function getDetails($location, $slug)
    {
        // If it exists already, use it
        if ( ! empty($this->_themes[$slug])) {
            return $this->_themes[$slug];
        }

        if ( ! is_dir($path = $location.$slug) and is_file($path.'/Theme.php')) {
            return false;
        }

        // Core theme or third party?
        $is_core = trim($location, '/') === APPPATH.'Theme';

        //path to theme
        $web_path = $location.$slug;

        $theme                 = new stdClass;
        $theme->slug           = $slug;
        $theme->is_core        = $is_core;
        $theme->path           = $path;
        $theme->web_path       = $web_path; // TODO Same thing as path?
        $theme->screenshot     = $web_path.'/screenshot.png';

        //load the theme Module.php file
        $details = $this->spawnClass($slug, $is_core);

        //assign values
        if ($details) {
            foreach (get_object_vars($details) as $key => $val) {
                if ($key === 'options' and is_array($val)) {
                    // only save to the database if there are no options saved already
                    if ( ! $this->pdb->table('theme_options')->where('theme', $slug)->get()) {
                        $this->saveOptions($slug, $val);
                    }
                }
                $theme->{$key} = $val;
            }
        }

        // Save for later
        $this->_themes[$slug] = $theme;

        return $theme;
    }

    /**
     * Index Options
     *
     * @param string $theme The theme to save options for
     * @param array $options The theme options to save to the db
     *
     * @return boolean
     */
    public function saveOptions($theme, $options)
    {
        foreach ($options as $slug => $values) {
            // build the db insert array
            $this->pdb->table('theme_options')->insert(array(
                'slug' => $slug,
                'title' => $values['title'],
                'description' => $values['description'],
                'default' => $values['default'],
                'type' => $values['type'],
                'value' => $values['default'],
                'options' => $values['options'],
                'is_required' => $values['is_required'],
                'theme' => $theme,
            ));
        }

        $this->cache->clear('theme_m');

        return true;
    }

    /**
     * Count the number of available themes
     *
     * @return int
     */
    public function count()
    {
        return $this->theme_infos == null ? count($this->get_all()) : count($this->_themes);
    }

    /**
     * Get the default theme
     *
     * @return string
     */
    public function get_default()
    {
        return $this->_theme;
    }

    /**
     * Set a new default theme
     *
     * @param string $input
     *
     * @return string
     */
    public function set_default($input)
    {
        if ($input['method'] == 'index') {
            return Settings::set('default_theme', $input['theme']);
        }
        elseif ($input['method'] == 'admin_themes') {
            return Settings::set('admin_theme', $input['theme']);
        }
    }

    /**
     * Spawn Class
     *
     * Checks to see if a Module.php exists and returns a class
     *
     * @param string $slug The folder name of the theme
     * @param bool $is_core
     *
     * @return array
     */
    private function spawnClass($slug, $is_core = false)
    {
        $path = $is_core ? APPPATH : ADDONPATH;

        // Before we can install anything we need to know some details about the module
        $file = "{$path}Theme/{$slug}/Theme.php";

        // Check the details file exists
        if ( ! is_file($file)) {
            $file = SHARED_ADDONPATH.'Theme/'.$slug.'/Theme.php';

            if ( ! is_file($file)) {
                return false;
            }
        }

        // Sweet, include the file
        include_once $file;

        // Now call the details class
        $class = 'Theme\\'.ucfirst(strtolower($slug)).'\\Theme';

        // Now we need to talk to it
        return class_exists($class) ? new $class : false;
    }

    /**
     * Delete Options
     *
     * @param string $theme The theme to delete options for
     *
     * @return boolean
     */
    public function delete_options($theme)
    {
        $this->cache->clear('theme_m');

        return $this->db
            ->where('theme', $theme)
            ->delete('theme_options');
    }

    /**
     * Get option
     *
     * @param array|string $params The where conditions to fetch the option by
     *
     * @return array
     */
    public function get_option($params = array())
    {
        return $this->db
            ->select('value')
            ->where($params)
            ->where('theme', $this->_theme)
            ->get('theme_options')
            ->row();
    }

    /**
     * Get options by
     *
     * @param array|string $params The where conditions to fetch options by
     *
     * @return array
     */
    public function get_options_by($params = array())
    {
        return $this->db
            ->where($params)
            ->get('theme_options')
            ->result();
    }

    /**
     * Get values by
     *
     * @param array|string $params The where conditions to fetch options by
     *
     * @return array
     */
    public function get_values_by($params = array())
    {
        $options = new stdClass;

        $query = $this->db
            ->select('slug, value')
            ->where($params)
            ->get('theme_options');

        foreach ($query->result() as $option)
        {
            $options->{$option->slug} = $option->value;
        }

        return $options;
    }

    /**
     * Update options
     *
     * @param array $input The values to update
     * @param string $slug The slug of the option to update
     *
     * @return boolean
     */
    public function update_options($slug, $input)
    {
        $this->db
            ->where('slug', $slug)
            ->update('theme_options', $input);

        $this->cache->clear('theme_m');
    }
}
