<?php
define('IN_OUN', true);
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."ads.php");
$arid = $id  +0;
$Strstates = "a.states <> 1";  
$sql = "SELECT arid,acid,name,comms,otherurl  FROM ".$pre."artitxt WHERE arid='".$arid."' and domain_id = '".$Aconf['domain_id']."'  limit 1";
$rowarticle  = $oPub->getRow($sql);
if($rowarticle[arid] != $arid) {
	$strMessage = '此文章已删除';
	echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='articles.php';</script>";
	exit;
} else {
    if($rowarticle['otherurl']) {
         $rowarticle['article_url'] =$rowarticle['otherurl'];
    }else{
        if($Aconf['rewrite']){
            $rowarticle['article_url'] = 'article-'.$arid.'-0.html';
        }else{
            $rowarticle['article_url'] = 'article.php?id='.$arid;
        }
    }   
}
$acid = $rowarticle['acid'];   
/* 评论提交  start */
include_once( ROOT_PATH."includes/artcommsinstall.php"); 
/* 评论提交  end */  

if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}   
/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */ 
$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$arid.$_REQUEST["page"]));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	//评论列表    
	$table = $pre."arti_comms";
	$strWhere = ($Aconf['support'])?" states = 3 ":" states <> 1 ";
	$strWhere = ' WHERE '.$strWhere.' AND arid='.$arid.' AND domain_id='.$Aconf['domain_id'];
	$count = $oPub-> getOne("SELECT count( * ) AS count FROM ".$pre."arti_comms" .$strWhere); 
	$rowartitxt["count"] = $count; //传送留言总数

	$page = new ShowPage;  
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count;
	$page->PHP_SELF = PHP_SELF;
	$pagenew = $page->PageNum();
	$page->LinkAry = array('id'=>$arid); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$rowarticle["showpage"]  = ($count > $Aconf['set_pagenum'])?$page->ShowLink_num():'';   
	$sql = "SELECT arcid,email,ip,name,descs,dateadd,support,against   FROM ".$table.$strWhere." ORDER BY  dateadd asc limit ".$strOffSet;
	$AsppAll = $oPub->select($sql);
	$n = ($_REQUEST["page"] > 1)? $Aconf['set_pagenum'] * ($_REQUEST["page"] - 1):0; 
	while( @list( $k, $v ) = @each( $AsppAll) ) {
		$n ++ ;
		$AsppAll[$k][dateadd] = date("y年n月j日h:i", $v[dateadd]);
		$Aip = explode(".",$v[ip]);
		$AsppAll[$k][ip] = '第'.$n.'楼 IP:'.$Aip[0].'.'.$Aip[1].'.*';
		$AsppAll[$k][nick] = empty($v[name])?'匿名':$v[name];
		$AsppAll[$k][descs] =  clean_html($v[descs]);
	}
	$rowarticle[arti_comms] = $AsppAll;  
	$Ahome["acomms"] = $rowarticle; 

	include_once( ROOT_PATH."includes/item_set.php"); 
	$acid =  $rowarticle["acid"];
	/* 当前位置导航 */ 
	$sql = "SELECT name,fid FROM ".$pre."articat  where  acid = '".$acid."' AND domain_id=".$Aconf['domain_id']." LIMIT 1";
	$row = $oPub->getRow($sql); 
	$fid = $row["fid"];  
	if($Aconf['rewrite']){
		$nowcatname =  '<a href="articles-'.$acid.'-0.html">'.$row["name"].'</a>'; 
		$articles = 'articles.html';
	}else{
		$nowcatname =  '<a href="articles.php?id='.$acid.'">'.$row["name"].'</a>';
		$articles = 'articles.php';
	}
 
	$strPrenave = pre_node($fid,$pre."articat","acid",$articles,true);

	$tmpnowNave = empty($nowcatname)?'':'<li>'.$nowcatname .$Aconf['nav_symbol'].'</li>'; 
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li><A HREF="'.$articles.'">'.$Aweb_url["articles"][0].'</a> '.$Aconf['nav_symbol'].'</li>'.$strPrenave.$tmpnowNave .'<li><span style="font-weight:lighter"> 评论</span></li>'; 
	$Aconf["header_title"] = $rowarticle["name"].'评论|'.$row["name"].'|'.$Aconf['header_title'];  

	 //同分类文章
	if($acid) {  
	   $idtype ="acid"; 
	   $tmpid  = next_node_all($acid,$pre."articat",$idtype,true);
	   $strID  = ($tmpid)?$acid.$tmpid:$acid;
	   $rowarticle["arti_catstxt"] = articles_list('top',10,$strID,'');  
	} 

    assign_template($Aconf);  
    $smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);
?>
