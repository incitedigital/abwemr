<?php

class cConfig
{
	var $type = "";
	function cConfig( $hOptions )
	{
		while( list($hKey, $hValue) = each($hOptions) )
		{
			$this->$hKey = $hValue;
		}
	}
}

?>