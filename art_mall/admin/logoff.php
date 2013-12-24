<?php 				 
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
$user->logout();
echo "<SCRIPT language='javascript'>top.location='../index.php';</script>"; 

?>