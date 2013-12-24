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
	$acid = $id + 0; 
	/* 得到当前分类的所有下级分类 start */ 
	$idtype ="acid"; 
	$tmpid  = next_node_all($acid,$pre."articat",$idtype,true);
	$strID  = ($tmpid)?$acid.','.$tmpid:$acid; 
	/* 得到当前分类的所有下级分类 end */
	/* 分类翻页 */
	$acids= ($acid > 0)?" AND a.acid in($strID) ":' '; 
	$Strstates = ($Aconf['article'])?' a.states = 2 ':' a.states <> 1 ';
	$where = $Strstates.' AND  a.domain_id = '.$Aconf['domain_id'].$acids;                                                                                         
	$filter['record_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'artitxt AS a WHERE  '. $where); 
	$page = new ShowPage; 
	$page->PageSize = $Aconf['set_pagenum'];
	$page->PHP_SELF = PHP_SELF;
	$page->Total = $filter['record_count']; 
	$pagenew = $page->PageNum(); 
	$page->LinkAry = array('id'=>$acid); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$Ahome["showpage"] = ($filter['record_count']> $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
	$orderby = 'arti_date'; $substr=30;
	$Ahome["articles"] = articles_list( $orderby, $strOffSet,$strID,$substr);                                                                         
/*	while( @list( $key, $value ) = @each( $Ahome["articles"]) ) {
	$db_table = $pre.'product_file';
    $sql = "SELECT * FROM ".$db_table." WHERE prid = ".$value['arid']." ";
    $img_list = $oPub->select($sql);
	$Ahome["articles"][$key]['filename']=$img_list;
	print_r($Ahome["articles"][$key]);
	}*/

	/* 当前分类的所有前置分类 用于当前位置导航 */ 
	$sql = "SELECT name,fid,descs,keywords FROM ".$pre."articat where  acid = '".$acid."' AND domain_id=".$Aconf['domain_id']." LIMIT 1"; 
	$row = $oPub->getRow($sql);
	$nowcatname = $row[name]; 
	$Aconf['description']  = $row["descs"];
	$Aconf['keywords']     = $row["keywords"]; 
	$fid = $row["fid"];  

	$idtype = "acid"; 
	$strPrenave = pre_node($fid,$pre."articat",$idtype,$Aconf["nowFile"],true);

	$tmpnowNave = empty($nowcatname)?'':'<li>'.$nowcatname.'</li>';
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A>'.$Aconf['nav_symbol'].'</li><li><A HREF="'.$Aconf["nowFile"].'">'.$Aweb_url[$Aconf["preFile"]][0].'</a>'.$Aconf['nav_symbol'].'</li>'.$strPrenave.$tmpnowNave; 
	$Aconf["header_title"] = $nowcatname.'|'.$Aconf['header_title']; 

    assign_template($Aconf); 
    $smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
} 
$smarty->display($Aconf["displayFile"], $cache_id); 
?>
