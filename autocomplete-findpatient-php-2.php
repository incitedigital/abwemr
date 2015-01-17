<?php
error_reporting(0);
set_error_handler("userErrorHandler");

// user defined error handling function
function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
{
	header("HTTP/1.0 500 Internal Error", true, 500);
	echo "<br />$errmsg in <b>$filename</b> on line <b>$linenum</b><br />";
}

require_once('Connections/dbc.php');
mysql_select_db($database_dbc);

//--jszone (DO NOT REMOVE THIS)

$table='tbl_patient';
$fields = array('fname:primary');
//--jszone (DO NOT REMOVE THIS)

function convert( $val )
{
	list($field,) = explode(':',$val);
	return $field;
}


$fields = implode(',',array_map('convert',$fields));


$sql = 'SELECT '.$fields.' FROM `'.$table.'`'; 

$mc = isset($_GET['mc'])?true:false;
$sa = isset($_GET['sa'])?true:false;

if (!empty($_GET['q'])) 
{
	$f = preg_split("/,/", $fields);
	
	$sql .= ' WHERE '.$f[0].' LIKE "';
	
	if (true === $mc) $sql .= "%";
	
	$sql .= mysql_real_escape_string($_GET['q']).'%"';
	
	if (true === $sa)
	{
		$fields = explode(',',$fields);
		
		$opt = array_slice($fields,1,count($fields));
		
		foreach ($opt as $key => $value)
		{
			if ($mc === true)
			{
				$sql .= ' OR "'.$value.'" LIKE "%'.mysql_real_escape_string($_GET['q']).'%"';
			}
			else 
			{
				$sql .= ' OR "'.$value.'" LIKE "'.mysql_real_escape_string($_GET['q']).'%"';
			}
		}	
	}
}

if (isset($_GET['limit']) && is_int($_GET['limit']))
{
	$sql .= ' LIMIT '.$_GET['limit'];
}

$res = mysql_query($sql);

header('Content-type: text/plain');
while ($row = mysql_fetch_array($res, MYSQL_NUM))
{
	echo join("|", $row)."\n";
}
?>