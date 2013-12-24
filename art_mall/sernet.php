<?php
define('IN_OUN', true);
include_once( "./includes/command.php");

include_once( ROOT_PATH."ads.php");
/* 产品页显示的模块 */   
$Aconf['header_title'] = $Aweb_url['about'][0]."|".$Aconf["web_title"]; 
$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A>'.$Aconf['nav_symbol'].'</li><li>'.$Aweb_url['sernet'][0].'</li>'; 
if ((DEBUG_MODE & 2) != 2){
    $smarty->caching = true;
}
/* 调用模板 */  
$cache_id = sprintf('%X', crc32($Aconf['domain_id']));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {
	include_once( ROOT_PATH."includes/item_set.php"); 
	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?> 