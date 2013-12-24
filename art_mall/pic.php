<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");

include_once( ROOT_PATH."ads.php"); 

 
$arid = $id = $id + 0; 
$Strstates = " and a.states <> 1 and a.ifpic > 0 "; 
if($arid > 0){
	$Strstates .= " and a.arid='".$arid."' ";	 
} 
$orders = ' order by a.top desc,a.arti_date desc  '; 
$sql = "SELECT a.acid,a.vtid,a.user_id,a.comms,a.support,a.against,a.hots,a.arti_thumb,a.arti_date,a.edit_comm,b.arid,b.sour,b.sourhttp,b.name,b.descs,b.cltion,b.cltion_product,b.cltion_topic FROM ".$pre."article as b,".$pre."artitxt as a
		WHERE  a.arid = b.arid
		AND b.domain_id=".$Aconf['domain_id'].$Strstates.$orders . 
	    " LIMIT 1"; 
$rowarticle = $oPub->getRow($sql);
if(!$rowarticle) {
   $strMessage = '无此图库不能访问！';
   echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='./';</script>";
   exit;
}

 
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/* 调用模板 */
//$themesPath = ROOT_PATH.'themes/warped/';
/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */ 

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$arid));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php"); 
	$Aconf['header_title'] = $Aweb_url['pic'][0]."|".$Aconf["web_title"];  

	$acid = $rowarticle['acid'];
	$arid = $_GET[arid];

	$rowarticle['arti_date']  = ($rowarticle['arti_date'])?date("Y年m月d日 h:s:i", $rowarticle['arti_date']):'';

	/* 文章翻页 start*/ 
	if(empty($rowarticle['edit_comm'])){
		$descs = clean_html($rowarticle['descs']);
		$rowarticle['edit_comm'] = sub_str($descs ,200,true); 
	}  

	/* 关联文章 */
	if($rowarticle['cltion'] != '')
	{
		$strCltion = '';
		$Acltion = explode("{|}",$rowarticle['cltion']);
		while( @list( $k, $v) = @each($Acltion) ) 
		{
		   $Akeysname = explode("[|]",$v);
		   $strCltion .= '<A HREF="'.$Akeysname[1].'">'.$Akeysname[0].'</A><br/>';
		}
		$rowarticle['cltion'] =  ($strCltion != '')?$strCltion:'';
	}
	$db_table = $pre.'arti_file';
	$sql = "SELECT fileid,thumb_url,filename,descs FROM " . $db_table . " WHERE arid = $rowarticle[arid]";
	$rowarticle[img_list] = $oPub->select($sql);
	if($rowarticle[img_list]) {
		$rowarticle[show_img_list] = true;
		while( @list( $k, $v) = @each( $rowarticle[img_list] ) ) { 
			$rowarticle["main_pic"] = $v["filename"];
			$rowarticle["main_pic_name"] = $v["descs"];
			break;
		}
	}
	/* 关联产品 */
	//上一篇，下一篇
	$sql = "SELECT arid,subname,name,min_thumb  FROM  ".$pre."artitxt  WHERE  states <> 1  and ifpic > 0 AND domain_id=".$Aconf['domain_id'].
		" and arid > ".$rowarticle[arid]." order by arid asc  LIMIT 1"; 
	$rowarticle["pre_arti"] = $oPub->getRow($sql);

	$sql = "SELECT arid,subname,name,min_thumb  FROM  ".$pre."artitxt  WHERE  states <> 1   and ifpic > 0  AND domain_id=".$Aconf['domain_id'].
		" and arid < ".$rowarticle[arid]." order by arid desc  LIMIT 1";
	$rowarticle["next_arti"] = $oPub->getRow($sql);

	if($Aconf['rewrite']){ 
		$rowarticle['pic_url'] = 'pic-'.$rowarticle['arid'].'.html';
		$rowarticle['article_url'] = 'article-'.$rowarticle['arid'].'-0.html'; 
		$rowarticle["pre_pic_url"] = 'pic-'.$rowarticle["pre_arti"]["arid"].'.html';
		$rowarticle["next_pic_url"] = 'pic-'.$rowarticle["next_arti"]["arid"].'.html';
	}else{
		$rowarticle['pic_url'] = 'pic.php?id='.$rowarticle['arid'];
		$rowarticle['article_url'] = 'article.php?id='.$rowarticle['arid'];
		$rowarticle["pre_pic_url"] = 'pic.php?id='.$rowarticle["pre_arti"]["arid"];
		$rowarticle["next_pic_url"] = 'pic.php?id='.$rowarticle["next_arti"]["arid"]; 
	} 
	/* 访问量 +1 */
	$db_table = $pre."artitxt ";
	$sql = "UPDATE ". $db_table." SET hots = hots + 1  WHERE arid = '".$_GET[arid]."'";
	$oPub->query($sql);
	 
	$Ahome["arti"] = $rowarticle; 
	$Ahome["nowNave"]  = '<li><A HREF="./">首页</A></li><li><A HREF="pics.php">'.$Aweb_url['pics'][0].'</a></li><li>'.$rowarticle[name].'</li>'; 
	$Aconf['header_title'] = $rowarticle[name].'|'.$Aconf['header_title']; 
	unset($rowarticle);

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);

?>
