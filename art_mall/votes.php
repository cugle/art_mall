<?php
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."ads.php"); 

//右侧 时尚调用 end     
if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}

$strCache_id = $Aconf['domain_id'].$page; 
$cache_id = sprintf('%X', crc32($str_cache_id));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {
	include_once( ROOT_PATH."includes/item_set.php");
	/* 产品页显示的模块 */   
	$Aconf['header_title'] = $Aweb_url['votes'][0]."|".$Aconf["web_title"];
	/* 调查列表 查询条件 */
	 
	$where = "a.states=0 AND a.is_show = 1 AND a.domain_id = ".$Aconf['domain_id'];
	$filter['record_count'] = $oPub->getOne("SELECT COUNT(*) as count FROM ".$pre."vote_title as a  WHERE 1 AND ". $where); 

	$page = new ShowPage; 
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $filter['record_count'];
	$pagenew = $page->PageNum();
	$page->PHP_SELF = PHP_SELF;
	$page->LinkAry = array(); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$Ahome["showpage"] = ($filter['record_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
	$sql = "SELECT a.vtid,a.vt_name,a.vt_desc,a.vt_nums,a.add_time 
		   FROM ".$pre."vote_title as a 
			WHERE  $where 
			ORDER BY a.add_time DESC   
			LIMIT ". $strOffSet;
	$row = $oPub->select($sql);
	if($row ) { 
		foreach ($row AS $key=>$val)
		{ 
			$row[$key]['add_time']  = ($val['add_time'])?date("y年m月d日", $val['add_time']):''; 
			if($Aconf['rewrite']){ 
				$row[$key]['vote_url'] = 'vote-'.$val[vtid].'-0.html';
			}else{
				$row[$key]['vote_url'] = 'vote.php?id='.$val[vtid];
			}  
		} 
	}
	$Ahome["votes"] = $row;unset($row);
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li>'.$Aweb_url['votes'][0].'</li>';  


    assign_template($Aconf); 
    $smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?>
