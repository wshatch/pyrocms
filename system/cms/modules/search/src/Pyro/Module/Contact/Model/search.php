<?php namespace Pyro\Module\Contact\Model;

use Illuminate\Database\Eloquent\Model;

class SearchIndex extends Model
{
    public static $table = 'search_index';

    /**
    * Creates a new Search index
    * @param array $input The data to insert
    * @return object 
    */
    public static function create($input)
    {
        $input = self::formatArray($input);
        self::delete($input);
        return parent::create($input)
    }
    /**
    * Removes the index
    */
    public function delete($input)
    {
    }

    public function filter($filter)
    {
    }

    public function count($query)
    {
    }
    public function search($query)
    {
    }

    /**
     * Formats the array.
     */
    private function formatArray($input)
    {
        $return_input = array();

		// Hand over keywords without needing to look them up
		if ( ! empty($options['keywords'])) {
			if (is_array($options['keywords'])) {
				$insert_data['keywords'] = impode(',', $options['keywords']);
			
			} elseif (is_string($options['keywords'])) {
				$insert_data['keywords'] = Keywords::get_string($options['keywords']);
				$insert_data['keyword_hash'] = $options['keywords'];
			}
		}

		// Store a link to edit this entry
		if ( ! empty($options['cp_edit_uri'])) {
			$insert_data['cp_edit_uri'] = $options['cp_edit_uri'];
		}

		// Store a link to delete this entry
		if ( ! empty($options['cp_delete_uri'])) {
			$insert_data['cp_delete_uri'] = $options['cp_delete_uri'];
		}


    }
}
