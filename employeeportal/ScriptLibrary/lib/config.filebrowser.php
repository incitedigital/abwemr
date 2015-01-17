<?php
/*
class.filebrowser.php settings
*/
define( 'CN_FOLDER', 0x01 );
define( 'CN_FILE', 0x02 );
define( 'CN_FILE_TYPE_HTML', 0x04 );
define( 'CN_FILE_TYPE_XML', 0x08 );
define( 'CN_FILE_TYPE_JS', 0x20 );
define( 'CN_FILE_TYPE_CSS', 0x40 );
define( 'CN_FILE_TYPE_IMAGE', 0x08 );
define( 'CN_FILE_TYPE_ARCHIVE', 0x10 );


define( 'CS_HTML', "(htm|html)$" );
define( 'CS_XML', "(xml)$" );
define( 'CS_JS', "(js)$" );
define( 'CS_CSS', "(css)$" );
define( 'CS_IMAGE', "(gif|png|jpg|jpeg)$" );
define( 'CS_ARCHIVE', "(rar|zip)$" );

global $aFBTypeMappings;
$aFBTypeMappings = array(	CN_FILE_TYPE_HTML   =>  CS_HTML, 
							CN_FILE_TYPE_XML   =>  CS_XML, 
							CN_FILE_TYPE_JS     =>  CS_JS, 
                            CN_FILE_TYPE_CSS    =>  CS_CSS, 
                            CN_FILE_TYPE_IMAGE  =>  CS_IMAGE, 
                            CN_FILE_TYPE_ARCHIVE=>  CS_ARCHIVE );

//those are names of directories that will stay hidden
define( 'CS_PROTECTED_DIR_NAMES', "^(js|script|inc|css|i|img|images)$" );

/*
start dir
please edit this paths
*/
//define( 'CS_START_DIR', "../data/" );
?>