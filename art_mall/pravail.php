<?php
/* 供货渠道 */
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."ads.php"); 



if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$page));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {
	include_once( ROOT_PATH."includes/item_set.php");
	$Aconf['header_title'] = $Aweb_url['pravail'][0]."|".$Aconf["web_title"]; 
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li> <A HREF="'.$Aconf["nowFile"].'">'.$Aweb_url[$Aconf["preFile"]][0].'</a>  </li>'; 
	$Aconf["header_title"] = $Aweb_url['pravail'][0].'|'.$Aconf['header_title'];  

	/* 所有渠道列表 */
	$where = " where  domain_id='". $Aconf['domain_id'] ."'";
	$sql = "SELECT COUNT(*) as count FROM ".$pre."pravail ".$where;
	$count= $oPub->getOne($sql); 
	$page = new ShowPage;
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count;
	$page->PHP_SELF = PHP_SELF;
	$pagenew = $page->PageNum();
	$page->LinkAry = array();
	$strOffSet = $page->OffSet(); 
	$showpage = ($count > $Aconf['set_pagenum'])?$page->ShowLink_num():'';
	$Ahome[showpage] = $showpage; 
	$sql   = "SELECT praid,pra_name,sets,shop_logo FROM ".$pre."pravail". $where  ." ORDER BY dateadd DESC  LIMIT ". $strOffSet;
	$ApravailAll  = $oPub->select($sql);
	$n = 0;
	if($ApravailAll){ 
		foreach ($ApravailAll AS $key=>$val) { 
			if($Aconf['rewrite']){  
				$ApravailAll[$key]['shop_url'] ='shop-'.$val["praid"].'.html';
			}else{  
				$ApravailAll[$key]['shop_url'] ='shop.php?id='.$val["praid"];
			}  
			$ApravailAll[$key]['descs']    =  sub_str(clean_html($val[descs]),100);

			$Asets = explode("{|}",$val['sets']); 
			if(count($Asets))
			foreach ($Asets AS $sv)
			{
			   $At = array();
			   $At = explode("[|]",$sv);
			   if($At[0])
			   {
				   $ApravailAll[$key][$At[0]] = $At[1];
				}
			}
		}

	}

	$Ahome["pravails"] = $ApravailAll;  

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 

?>
