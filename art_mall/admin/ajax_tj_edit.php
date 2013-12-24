<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//op=" + edit_price + "&cgid=" + cgid 

$cgid = $_GET['cgid'] + 0;
$edit_val = getUtf8( "$_GET[edit_val]");
$tjcatid  = $_GET['tjcatid'];
$str = '';
if($cgid) {
	$db_table = $pre."tj";
        /* 置顶 */
        if($_GET['op'] == 'tj') {
           $sql = "UPDATE " . $db_table . " SET 
			      `orders`='".$edit_val."' 
	               WHERE `cgid` ='".$cgid."' and domain_id=".$Aconf['domain_id'];
           $oPub->query($sql);
		   $str = '<span style="cursor:pointer" onmousedown="return chengw_list_edit(\'tj\',\''.$cgid.'\','.$edit_val.','.$tjcatid.')">'.$edit_val.'</span>';
       } 
        $sql = "UPDATE " . $db_table . " SET 
			      `orders`='99' 
	               WHERE `cgid` <> ".$cgid. " and  orders = '".$edit_val."' and tjcatid='".$tjcatid."'  and domain_id=".$Aconf['domain_id'];
         $oPub->query($sql);

}
echo $str;
?>