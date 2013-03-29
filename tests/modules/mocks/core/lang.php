<?php

class Mock_Core_Lang extends CI_Lang {

	function line($line = '', $log_errors=FALSE)
	{
		return FALSE;
	}

	function load($langfile, $idiom = '', $return = false, $add_suffix = true, $alt_path = '')
	{
		return;
	}

}
