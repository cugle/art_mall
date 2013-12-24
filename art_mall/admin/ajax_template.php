<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//op=" + edit_price + "&arcid=" + arcid 

 
$edit_val = getUtf8( $edit_val );
$str = 'sssssss';
if(!empty($edit_val))
{
 
	$str = $edit_val;
}
echo $str;
?>