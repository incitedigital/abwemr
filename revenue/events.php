<?php
// Values received via ajax
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];
$url = $_POST['url'];
// connection to the database


mysql_pconnect("localhost", "nacprodt_emr", "diamondpony8") or die("Could not connect");
mysql_select_db("nacprodt_emr") or die("Could not select database");

$sql = "SELECT * FROM evenement ORDER BY id";
$rs = mysql_query($sql);
$arr = array();

while($obj = mysql_fetch_object($rs)) {
$arr[] = $obj;
}
echo json_encode($arr);
?>

