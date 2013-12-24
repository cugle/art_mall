<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//op=" + edit_price + "&arcid=" + arcid 

$arcid = $_GET['arcid'] + 0;
$edit_val = getUtf8( "$_GET[edit_val]");
$str = '';
if($arcid)
{
        /* 置顶 */
        if($_GET['op'] == 'top')
       {
           $db_table = $pre."arti_comms";
	       $edit_val = ($edit_val == 1)?0:1;
           $sql = "UPDATE " . $db_table . " SET 
			      `top`='".$edit_val."' 
	               WHERE `arcid` =".$arcid." and `domain_id`=".$Aconf['domain_id'];
           $oPub->query($sql);
           
		   $tmpstr = ($edit_val)?'是':'否';
		   $str = '<span style="cursor:pointer" onmousedown="return art_list_edit(\'top\',\''.$arcid.'\','.$edit_val.')">'.$tmpstr.'</span>';
       }

}
echo $str;
?>