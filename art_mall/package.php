<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");
 

 
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/* 调用模板 */
//$themesPath = ROOT_PATH.'themes/warped/';
/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */ 

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$arid));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php"); 
	$Aconf['header_title'] = $Aweb_url['package'][0]."|".$Aconf["web_title"];   
	$Ahome["nowNave"]  = '<li><A HREF="./">首页</A></li><li>建站套餐</li>';  

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);

?>
