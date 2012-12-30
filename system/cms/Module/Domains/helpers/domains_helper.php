<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author 		Ryan Thompson - AI Web Systems, Inc.
 * @package		PyroCMS\Core\Modules\Domains\Helpers
 */

/**
 * Get the site's ID based on SITE_REF
 */
if ( ! function_exists('site_id'))
{
	function site_id()
	{
		// Run query
		$r = ci()->db->query("SELECT id FROM core_sites WHERE ref = '".ci()->db->escape_str(SITE_REF)."' LIMIT 1")->row();

		return $r->id;
	}
}