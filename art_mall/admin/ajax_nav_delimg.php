<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false); 

$value		= getUtf8( $_GET["avalue"]); 
$op			= $op;
$str = '';
if( $op == 'del' && is_file('../data/weblogo/' . $value) ) {  
    @unlink('../data/weblogo/' . $value); 
}  
echo '';
?>