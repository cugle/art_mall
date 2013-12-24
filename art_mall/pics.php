<?php
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."ads.php");//本站广告部分不缓存 
/* 缓存编号 */
if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}
$str_cache_id = $Aconf['domain_id'].$id.$page; 
$cache_id = sprintf('%X', crc32($str_cache_id));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {  
	
	include_once( ROOT_PATH."includes/item_set.php"); 
	$Aconf['header_title'] = $Aweb_url[$Aconf["preFile"]][0]."|".$Aconf["web_title"]; 

	/* 得到当前分类的所有下级分类 end */
	/* 分类翻页 */ 
	$where = " a.states <> 1  and a.ifpic > 0 and   a.domain_id = ".$Aconf['domain_id'];                                                                                         
	$filter['record_count'] = $oPub->getOne("SELECT COUNT(*) as count from ".$pre."artitxt as a  where  ". $where); 
	$page = new ShowPage;  
	$page->PageSize = $Aconf['set_pagenum'];
	$page->PHP_SELF = PHP_SELF;
	$page->Total = $filter['record_count']; 
	$pagenew = $page->PageNum(); 
	$page->LinkAry = array('id'=>$acid); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$Ahome["showpage"] = ($filter['record_count']> $Aconf['set_pagenum'])?$page->ShowLink_num():''; 

	$row = $oPub->select("SELECT a.* from ".$pre."artitxt as a  where ".$where." order by a.arid  desc limit ".$strOffSet);
	while( @list( $k, $v ) = @each( $row ) ) { 
		if($Aconf['rewrite']){ 
			$row[$k]['pic_url'] = 'pic-'.$v['arid'].'.html';  
		}else{
			$row[$k]['pic_url'] = 'pic.php?id='.$v['arid'];  
		} 
 
	}

	$Ahome["pics"] = $row;                                                                        

	/* 当前分类的所有前置分类 用于当前位置导航 */ 
 
	$Aconf['description']  = $row["descs"];
	$Aconf['keywords']     = $row["keywords"]; 
	$fid = $row["fid"];  

 

	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A></li> <li>'.$Aweb_url[$Aconf["preFile"]][0].'</a></li>'; 
	$Aconf["header_title"] = $Aconf['header_title']; 

    assign_template($Aconf); 
    $smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
} 
$smarty->display($Aconf["displayFile"], $cache_id); 



?>
