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

if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$prid));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php"); 
	include_once( ROOT_PATH."includes/shopcomm.php");  
	//分类id与翻页在一个变量
	if($prapcid > 0 ){
	   $sql = "SELECT name from ".$pre."pravail_productcat where prapcid  = '".$prapcid."' and domain_id=".$Aconf['domain_id']." limit 1";
	   $prapcneme = $oPub->getOne($sql);
	   $strTmp = '<li> '.$prapcneme.'</li>';  
	}else
	{
		$strTmp = '';
	}
	/* 产品页显示的模块 */
	$Ahome["nowNave"]  = '<li><a href="'.$rowpra['shop_url'].'">'.$rowpra[pra_name].'</a></li><li><A HREF="'.$rowpra['shop_h'].'">热卖商品</A></li>'.$strTmp; 
	$Aconf["header_title"] =  $prapcneme.'|热卖商品|'.$rowpra[pra_name].'|'.$Aconf['header_title'];  

	/* 得到产品列表 */
	$tmpStr = ($prapcid>0)?" AND prapcid='".$prapcid."'":'';
	$where = "states=0 and  praid='".$praid."' AND domain_id = '".$Aconf['domain_id']."'".$tmpStr;
	$sql = "SELECT COUNT(*) as count FROM ". $pre."pravail_producttxt WHERE 1 AND ". $where;
	$count = $oPub->getOne($sql);  
	$page = new ShowPage;
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count;
	$page->PHP_SELF = PHP_SELF;
	$pagenew = $page->PageNum();
	$page->LinkAry = array('id'=>$praid.'a'.$prapcid); 
	$strOffSet = $page->OffSet(); 
	$Ahome["showpage"] = ($count > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
	$sql = "SELECT  prid, main_prid ,pacid,  name, shop_price,up_date,dateadd,comms,hots,min_thumb,shop_thumb from ".$pre."pravail_producttxt    
			where  $where order by prid desc limit ". $strOffSet;
	$rowproduct = $oPub->select($sql);
	if( $rowproduct ) {      
	   foreach ($rowproduct AS $key=>$val) {
			/* 查询是否为总站商品 */
			if($val[main_prid] > 0 ) {
				$db_table = $pre."producttxt";
				$where = " prid = '".$val[main_prid]."' and states=0 and domain_id = '".$Aconf['domain_id']."'";
				$sql = "SELECT name,shop_price,pacid,up_date,min_thumb,shop_thumb FROM ".$db_table." as a where 1 and ". $where.' limit 1';
				$rowtmp = $oPub->getRow($sql);
				$rowproduct[$key]['name'] = $rowtmp[name];
				$rowproduct[$key]['shop_price'] = $rowtmp[shop_price];
				$rowproduct[$key]['pacid'] = $rowtmp[pacid];
				$rowproduct[$key]['min_thumb'] = $rowtmp[min_thumb];
				$rowproduct[$key]['shop_thumb'] = $rowtmp[shop_thumb];
				$rowproduct[$key]['up_date']  = ($rowtmp['up_date'])?date("y年m月d日", $rowtmp['up_date']):'';
			}else{
				$rowproduct[$key]['up_date']  = ($val['up_date'])?date("y年m月d日", $val['up_date']):'';
			}

			if($Aconf['rewrite']){ 
				$rowproduct[$key]['shop_ht'] = 'shop_ht-'.$praid.'-'.$val[prid].'.html';
			}else{ 
				$rowproduct[$key]['shop_ht'] = 'shop_ht.php?id='.$praid.'&prid='.$val[prid];
			}  
		}
	}
	$rowpra['prapro'] = $rowproduct;unset($rowproduct);
	$Ahome["pravail"] = $rowpra; unset($rowpra);

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?>
 