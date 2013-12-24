<?php
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."ads.php"); 





if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
} 
/* 缓存编号 */
$strCache_id = $Aconf['domain_id'].$id.$page;
$cache_id = sprintf('%X', crc32($str_cache_id));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php");

	$prbid  = $id;  
	$db_table = $pre.'probrand '; 
	$sql = "SELECT  prbid,brand_name,site_url,brand_logo,brand_desc FROM ".$db_table."    
		WHERE  prbid='".$prbid."' AND  domain_id = '".$Aconf['domain_id']."'";
	$rowprobrand = $oPub->getRow($sql);
	if(!$rowprobrand) {
		$strMessage = '此品牌不存在！'; 
		echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='brands.php';</script>";
		exit;
	}  
	 

	$Aconf['header_title'] = $Aweb_url['brand'][0]."|".$Aconf["web_title"]; 

	$where = " a.states=0 AND a.prbid='".$prbid."' AND  a.domain_id = '".$Aconf['domain_id']."'";
	$sql = "SELECT COUNT(*) as count FROM ".$pre."producttxt  AS a WHERE 1 AND ". $where;
	$rowprobrand["count"] = $oPub->getOne($sql); 
	$page = new ShowPage; 
	$page->PageSize = $Aconf['set_pagenum'];
	$page->PHP_SELF = PHP_SELF;
	$page->Total = $rowprobrand["count"];
	$pagenew = $page->PageNum();
	$page->LinkAry = array('id'=>$prbid); 
	$strOffSet = $page->OffSet();
	$Ahome["showpage"] = ($rowprobrand["count"]  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 

	$sql = "SELECT a.prid,a.pcid,a.name,a.shop_price,a.s_discount,a.s_dis_exp,a.colors,a.up_date,a.dateadd,a.comms,a.min_thumb,a.shop_thumb       
		FROM ".$pre."producttxt as a,".$pre."product as b   
		WHERE  $where 
		AND a.prid = b.prid 
		ORDER BY a.top DESC,up_date desc  
		LIMIT ". $strOffSet;
	$rowproduct = $oPub->select($sql); 
	if($rowproduct ){ 
		foreach ($rowproduct AS $key=>$val) { 
			if($val['colors']){
				$rowproduct[$key]['name'] =  '<span style="color:'.$val['colors'].'">'.$val[name].'</span>'; 
			} 
			if(!$val['min_thumb']){
				$rowproduct[$key]['min_thumb'] = 'images/command/no_imgs.png';
			}

			if(!$val['shop_thumb']){
				$rowproduct[$key]['shop_thumb'] = 'images/command/no_imgsbig.png';
			}	
			
			$rowproduct[$key]['shop_price'] = ($val[shop_price] == '0.00')?'':$val[shop_price];
			$rowproduct[$key]['s_discount'] = ($val[s_discount] == '0.00')?'':$val[s_discount];
			$rowproduct[$key]['dateadd']  = ($val['dateadd'])?date("Y年m月d日h:i", $val['dateadd']):'';
			$rowproduct[$key]["up_date"]  = ($val["up_date"] > 0)?date("y年n月j日",$val["up_date"]):'dddddddd';  
			$sql = "SELECT name FROM ".$pre."productcat WHERE pcid=$val[pcid]";
			$row = $oPub->getRow($sql);
			$rowproduct[$key]['pcname'] = $row[name]; 

			if($Aconf['rewrite']){
				$rowproduct[$key]['product_url'] = 'product-'.$val[prid].'.html';
				$rowproduct[$key]['pcomms_url'] = 'product_comms-'.$val[prid].'.html';
				$rowproduct[$key]['pcname_url'] = 'products-'.$val[pcid].'-0.html';
			}else{
				$rowproduct[$key]['product_url'] = 'product.php?id='.$val[prid];
				$rowproduct[$key]['pcomms_url'] = 'product_comms.php?id='.$val[prid];
				$rowproduct[$key]['pcname_url'] = 'products.php?id='.$val[pcid];
			} 
		}
	} 
	$rowprobrand["product"] = $rowproduct;unset($rowproduct);

	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li><A HREF="'.$Aweb_url['brands'][1].'">'.$Aweb_url['brands'][0].'</a> '.$Aconf['nav_symbol'].'</li><li> '.$rowprobrand["brand_name"].'</li>'; 
	$Aconf["header_title"] = $rowprobrand["brand_name"].'|'.$Aweb_url['brands'][0].'|'.$Aconf['header_title']; 
	$Ahome["probrand"] = $rowprobrand;unset($rowprobrand);

    assign_template($Aconf); 
    $smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);  
?>
