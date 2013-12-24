<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");

$Aid = explode("a",$id);
$praid = $id = $Aid[0] + 0;//商户ID
if($Aid[1] > 0){
	$prapcid = $Aid[1] + 0;//商户自定义分类
}else{
	$prapcid = 0;
}


if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$prid));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php"); 
	include_once( ROOT_PATH."includes/shopcomm.php");  

	/* 评论数量 */
	$where_comms = "praid  = '".$praid."' AND states <> 1 AND domain_id='".$Aconf['domain_id']."'"; 
	$rowpra['comms'] = $oPub->getOne("SELECT COUNT(*) as count FROM ".$pre."pravail_product_comms  where ".$where_comms); 
	/* 产品页显示的模块 */ 
	$Ahome["nowNave"]  = '<li><a href="'.$rowpra['pra_name_url'].'">'.$rowpra[pra_name].'</a></li>';  
	$Aconf["header_title"] = $rowpra[pra_name].'|'.$Aconf['header_title']; 
	/* 找到所有的自定义分类 select start*/ 
	$Ahome["pravail"] = $rowpra;

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?> 