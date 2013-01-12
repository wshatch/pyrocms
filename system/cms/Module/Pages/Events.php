<?php namespace Module\Pages;

/**
 * Pages Event Class
 *
 * Current functionality is to add chunks field type.
 * 
 * @author     PyroCMS Dev Team
 * @package    PyroCMS\Core\Modules\Pages
 */
class Events {
 
    public function __construct()
    {
        \Library\Events::register('streams_core_add_addon_path', array($this, 'add_pages_ft_folder'));
    }
 
    /**
     * Add pages field_types folder to the
     * field type folder list. 
     *
     * @return	void
     */
    public function add_pages_ft_folder($type)
    {
        if (ci()->pdb->hasTable('page_chunks'))
        {
            $type->add_ft_path('pages_ft_path', APPPATH.'modules/pages/field_types/');
        }
    }

}