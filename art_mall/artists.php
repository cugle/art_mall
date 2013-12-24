<?php
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."ads.php"); 



if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
} 
/* 缓存编号 */
$strCache_id = $Aconf['domain_id'].$page;
$cache_id = sprintf('%X', crc32($str_cache_id));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php");
	$Aconf['header_title'] = $Aweb_url['brands'][0]."|".$Aconf["web_title"];

	$db_table = $pre.'probrand '; 
	$where = "domain_id = '".$Aconf['domain_id']."' AND is_show = 1 ";
	$count = $oPub->getOne("SELECT COUNT(*) as count FROM ".$db_table." AS a WHERE 1 AND ". $where); 
	$page = new ShowPage;  
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count;
	$page->PHP_SELF = PHP_SELF;
	$pagenew = $page->PageNum();
	$page->LinkAry = array(); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$Ahome["showpage"] = ($count  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
	$sql = "SELECT  prbid,brand_name,brand_logo,site_url,brand_desc FROM ".$db_table." WHERE  $where ORDER BY sort_order ASC LIMIT ". $strOffSet; 
	$rowpr = $oPub->select($sql);
	if($rowpr) { 
		foreach ($rowpr AS $k=>$v) {  
			$db_table = $pre."producttxt";
			$where = $strTmp." a.states=0 AND prbid='".$v[prbid]."' AND  a.domain_id = '".$Aconf['domain_id']."'";
			$sql = "SELECT COUNT(*) as count FROM ".$db_table." AS a WHERE 1 AND ". $where;
			$rowpr[$k][count] = $oPub->getOne($sql); 
			if($Aconf['rewrite']){  
				$rowpr[$k]['brand_url'] ='brand-'.$v["prbid"].'-0.html';
			}else{  
				$rowpr[$k]['brand_url'] ='brand.php?id='.$v["prbid"];
			} 
			$rowpr[$k]['brand_logo']=($v['brand_logo'])?'data/brandlogo/'.$v['brand_logo']:'';
		}
		
	}
	$Ahome['brands'] = $rowpr; 

	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li> <A HREF="'.$Aconf["nowFile"].'">'.$Aweb_url[$Aconf["preFile"]][0].'</a></li>'; 
	$Aconf["header_title"] = $Aweb_url['brands'][0].'|'.$Aconf['header_title']; 

    assign_template($Aconf); 
    $smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);  
?>
