<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");
include_once( ROOT_PATH."includes/item_set.php");
include_once( ROOT_PATH."ads.php");
/* 产品页显示的模块 */   
$Aconf['header_title'] = $Aweb_url['search'][0]."|".$Aconf["web_title"]; 
$op = $op?$op:0;
$id = $id?$id:0;
$name   = clean_html($name);
$stype1 = $stype2  =$stype3 = ''; 
$stype  = !$stype?2:$stype;
$Ahome['stype'] = $stype;
if($stype == 1) {
		//文章
	$Ahome['stype1'] = 'selected';
	if(!empty($name))
	{ 
		$where = ($Aconf['article'])?" a.states = 2 ":" a.states <> 1 ";
		$where .= ' and a.domain_id = '.$Aconf['domain_id'].' and a.name LIKE "%'.$name.'%"';  
		$where_ext = ' a.name LIKE "%'.$name.'%"';
	}else
	{
		$where = ($Aconf['article'])?" a.states = 2 ":" a.states <> 1 ";
		$where .= ' and a.domain_id = '.$Aconf['domain_id'];  
		$where_ext = '';	
	}
	$filter['record_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'artitxt AS a WHERE '. $where);   
	$pageop = new ShowPage;  
	$pageop->PageSize = $Aconf['set_pagenum'];
	$pageop->PHP_SELF = PHP_SELF;
	$pageop->Total = $filter['record_count']; 
	$pagenew = $pageop->PageNum(); 
	$pageop->LinkAry = array('op'=>$op,'id'=>$id,'name'=>$name,'stype'=>$stype); 
	$strOffSet = $pageop->OffSet();
	/* 翻页 */
	$Ahome['showpage'] = ($filter['record_count']  > $Aconf['set_pagenum'])?$pageop->ShowLink_num():''; 
	$orderby = 'arti_date';$acid = '';$substr=40;
	$Ahome['articles'] = articles_list( $orderby, $strOffSet,$acid,$substr,$where_ext);                                                                         
}elseif($stype == 2){
	$Ahome['stype2'] = 'selected';
	//产品 
	if(!empty($name))
	{ 
		$where = ' states <> 1 and  domain_id = '.$Aconf['domain_id'].' and  name LIKE "%'.$name.'%"';  
		$where_ext = ' and name LIKE "%'.$name.'%"';
	}else
	{
		$where = ' states <> 1  and  domain_id = '.$Aconf['domain_id'];  
		$where_ext = '';	
	}
	$filter['record_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'producttxt  WHERE '. $where);   
	$pageop = new ShowPage;  
	$pageop->PageSize = $Aconf['set_pagenum'];
	$pageop->PHP_SELF = PHP_SELF;
	$pageop->Total = $filter['record_count']; 
	$pagenew = $pageop->PageNum(); 
	$pageop->LinkAry = array('op'=>$op,'id'=>$id,'name'=>$name,'stype'=>$stype); 
	$strOffSet = $pageop->OffSet();
	/* 翻页 */
	$Ahome['showpage'] = ($filter['record_count']  > $Aconf['set_pagenum'])?$pageop->ShowLink_num():''; 
	$orderby = 'up_date desc ';$pcid = '';$substr=40;
	$Ahome["products"] = products_list( $orderby, $strOffSet ,$pcid,$where_ext );  

}elseif($stype == 3){
	$Ahome['stype3'] = 'selected';
	//商家

}
 
$Ahome['nowNave']  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li><A HREF="'.$Aweb_url["search"][1].'">'.$Aweb_url['search'][0].'</A> '.$Aconf['nav_symbol'].'</li><li>'.$name.'</li>'; 
$Aconf['header_title'] = $name.'|'.$Aweb_url['search'][0].'|'.$Aconf['header_title']; 

//右侧 时尚调用 end     
if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}

$strCache_id = $Aconf['domain_id'].$acid.$page; 
$strCache_id  = '';
$cache_id = sprintf('%X', crc32($str_cache_id));
//if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {
    assign_template($Aconf); 
    $smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome);
    /* 页面中的动态内容 */
 
//}
$smarty->display($Aconf["displayFile"], $cache_id);  
?>
