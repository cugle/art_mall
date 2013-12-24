<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false); 

$viid = $viid + 0;  
$edit_val = getUtf8( $edit_val);  
if( $viid > 0 ) {
	//orders vi_nums
	/* 置顶 */
	if($op == 'orders')
	{
	   $oPub->query("UPDATE " . $pre . "vote_item SET orders='".$edit_val."' WHERE `viid` ='".$viid."' and domain_id=".$Aconf['domain_id']);  
	   $str = '<INPUT TYPE="text" style="background-color: #EBEBEB" value="'.$edit_val.'" size="2" onDblClick=vote_edit(\'orders\',this.value,\''.$viid.'\') />';  
	}elseif($op == 'vi_nums')
	{
	   $oPub->query("UPDATE " . $pre . "vote_item SET `vi_nums`='".$edit_val."' WHERE `viid` ='".$viid."' and domain_id=".$Aconf['domain_id']);  
	   $str = '<INPUT TYPE="text" style="background-color: #EBEBEB" value="'.$edit_val.'" size="2" onDblClick=vote_edit(\'vi_nums\',this.value,\''.$viid.'\') />'; 
	}
}
echo $str;
?>