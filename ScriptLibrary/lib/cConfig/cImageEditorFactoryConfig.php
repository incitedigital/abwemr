<?php

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "cConfig.php");

class cImageEditorFactoryConfig extends cConfig
{
	function cImageEditorFactoryConfig( $hOptions )
	{
		$this->cConfig( $hOptions );
		/*FIXIT*/
		/*Add netpbm detection*/
		$this->aAvailableLibraries = array( "gd2" => function_exists( "imagealphablending" ), "gd" => function_exists( "gd_info" ), "imagemagick" => function_exists( "magickwand" ) );
	}
}


?>