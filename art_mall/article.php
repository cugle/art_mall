<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");
include_once( ROOT_PATH."ads.php");  
 
$arid = $id +0; $pnid = $pnid +0; 
$pnid = $pnid < 1?1:$pnid;
//判断是否启用审核,对于正常文章0/1/2  没审核0/删除1/审核通过2 
$Strstates = ($Aconf['article'])?" a.states = 2 ":" a.states <> 1 ";
$sql = "SELECT a.acid,a.vtid,a.ifpic,a.user_id,a.comms,a.support,a.against,a.hots,a.arti_thumb,a.arti_date,a.edit_comm,b.arid,b.sour,b.sourhttp,b.name,b.descs,b.cltion,b.cltion_product,b.cltion_topic FROM ".$pre."article as b,".$pre."artitxt as a WHERE  a.arid = '".$arid."' AND  ".$Strstates."  AND a.arid = b.arid AND b.domain_id=".$Aconf['domain_id']. " LIMIT 1"; 
$rowarticle = $oPub->getRow($sql);
if(!$rowarticle) {
   $strMessage = $Aweb_desc["article_del"];
   echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='articles.php';</script>";
   exit;
}
$acid = $rowarticle['acid'];  
$allow = true;
$Ra = $oPub->getRow('SELECT allowjob  FROM '.$pre.'articat where acid = "'.$acid.'"');

$rowarticle['allowjob'] = $Ra['allowjob']; //允许求职简历
/* 访问量 +1 */
$oPub->query("UPDATE ". $pre."artitxt  SET hots = hots + 1 ".$updatevtid." WHERE arid = '".$arid."' limit 1"); 

/* 评论提交  start */
include_once( ROOT_PATH."includes/artcommsinstall.php"); 
/* 评论提交  end */ 
 
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
$smarty->caching = false;
/* 调用模板 */
//$themesPath = ROOT_PATH.'themes/warped/';
/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */ 

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$arid.$pnid));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php");  
	$Aconf['header_title'] = $Aweb_url['articles'][0]."|".$Aconf["web_title"]; 
	$Aconf['description']  = '';
	$Aconf['keywords']     = ''; 
	//新闻主体 start  
	$rowarticle['arti_date']  = ($rowarticle['arti_date'])?date("y年n月j日H:i", $rowarticle['arti_date']):'';
	$rowarticle['pnid'] = $pnid; 
	/* 文章翻页 start*/ 
	//$Adescs =preg_split('/<div style="page-break-after: always">\s*<span style="display: none;">&nbsp;<\/span><\/div>/',$rowarticle['descs'], -1); 
	$Adescs =preg_split('/<hr style="page-break-after:always;" class="ke-pagebreak" \/>\s*<p>\s*<br \/>\s*<\/p>/',$rowarticle['descs'], -1);
 


	$articel_page = '';
	if(count($Adescs) > 1) {	
		 $n = 1;
		 while( @list( $k, $v) = @each($Adescs) ) { 
			if($pnid == $n) {
				$style = 'style="background-color: #3891B1;color:#FFFFFF"';
				if($Aconf['rewrite']){
					$articel_page .= '<a href="'.$Aconf["preFile"].'-'.$arid.'-'.$n.'.html" '.$style.'>'.$n.'</a> ';
				}else{
					$articel_page .= '<a href="'.PHP_SELF.'?id='.$arid.'&pnid='.$n.'" '.$style.'>'.$n.'</a> ';
				} 
				$rowarticle['descs'] = $v;
			} else { 
				if($Aconf['rewrite']){
					$articel_page .= '<a href="'.$Aconf["preFile"].'-'.$arid.'-'.$n.'.html">'.$n.'</a> ';
				}else{
					$articel_page .= '<a href="'.PHP_SELF.'?id='.$arid.'&pnid='.$n.'">'.$n.'</a> ';
				} 
			}
			$n ++ ;
		} 
	}else{
		$rowarticle['descs'] = $Adescs[0];
	} 
	$rowarticle['articel_page'] = $articel_page;  
	 /* 文章翻页 end*/
	if (!$rowarticle["sour"]) {
		$rowarticle["sour"]     = $Aconf['web_title'];
		$rowarticle["sourhttp"] = $Aconf['domain_url'];
	} 

	if($Aconf['rewrite']){ 
		$rowarticle['arcomms_url'] = 'acomms-'.$rowarticle['arid'].'-0.html';
		$rowarticle['pic_url'] = 'pic-'.$rowarticle['arid'].'.html';
	}else{
		$rowarticle['arcomms_url'] = 'acomms.php?id='.$rowarticle['arid'];
		$rowarticle['pic_url'] = 'pic.php?id='.$rowarticle['arid'];
	} 
	/* 关联文章 */
	if($rowarticle['cltion'] != '') {
		$strCltion = '';
		$Acltion = explode("{|}",$rowarticle['cltion']);
		$n = 1;
		$AstrCltion = array();
		while( @list( $k, $v) = @each($Acltion) ) {
		   $Akeysname = explode("[|]",$v);
		   if($Akeysname[0]) {
				 $n ++ ;
				 $AstrCltion[$n] = '<A HREF="'.$Akeysname[1].'">'.$Akeysname[0].'</A>';
			}
		}
		if(count($AstrCltion) > 0) {
			 $rowarticle['cltion'] =$AstrCltion;
		} else {
			 $rowarticle['cltion'] = false;
		}
	}

	$sql = "SELECT thumb_url,filename,descs FROM " . $pre."arti_file WHERE arid = $rowarticle[arid]";
	$rowarticle[img_list] = $oPub->select($sql);
	if($rowarticle[img_list]) {
		$rowarticle[show_img_list] = true;
	}
	/* 关联产品 */
	/* 责任编辑 */
	$rowarticle['user_id'] = $oPub->getOne("SELECT user_name FROM ".$pre."users WHERE  id = '".$rowarticle['user_id']."'"); 

	//新闻主体结束 
	/* 当前位置导航 */ 
	$row = $oPub->getRow("SELECT name,fid FROM ".$pre."articat where  acid = '".$rowarticle['acid']."' AND domain_id=".$Aconf['domain_id']." LIMIT 1"); 
	$fid = $row[fid];  
	if($Aconf['rewrite']){
		$nowcatname =  '<a href="articles-'.$rowarticle[acid].'-0.html">'.$row[name].'</a>'; 
		$articles = 'articles.html';
	}else{
		$nowcatname =  '<a href="articles.php?id='.$rowarticle[acid].'">'.$row[name].'</a>';
		$articles = 'articles.php';
	}
	$idtype = "acid";
	$strPrenave = pre_node($fid,$pre."articat",$idtype,$articles,true);
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A>'.$Aconf['nav_symbol'].'</li><li><A HREF="'.$articles.'">'.$Aweb_url["articles"][0].'</a>'.$Aconf['nav_symbol'].'</li>'.$strPrenave.'<li>'.$nowcatname.$Aconf['nav_symbol'].'</li><li><span style="font-weight:lighter"> detail</span></li>'; 
	$Aconf["header_title"] = $rowarticle['name'].'|'.$row['name'].'|'.$Aconf['header_title'];  
	/* 当前位置导航负值结束 */

	//上一篇，下一篇
	$sql = "SELECT arid,subname,name,min_thumb  FROM  ".$pre."artitxt  WHERE  states <> 1  and  acid = '".$rowarticle['acid']."' AND domain_id=".$Aconf['domain_id'].
		" and arid < ".$rowarticle['arid']." order by arid desc  LIMIT 1"; 
	$rowarticle["pre_arti"] = $oPub->getRow($sql);

	$sql = "SELECT arid,subname,name,min_thumb  FROM  ".$pre."artitxt  WHERE  states <> 1   and  acid = '".$rowarticle['acid']."'  AND domain_id=".$Aconf['domain_id'].
		" and arid > ".$rowarticle['arid']." order by arid asc  LIMIT 1";
	$rowarticle["next_arti"] = $oPub->getRow($sql); 
	if($Aconf['rewrite']){  
		if($rowarticle["pre_arti"])
		{
			$rowarticle["pre_article_url"] = 'article-'.$rowarticle["pre_arti"]["arid"].'-0.html'; 
		}

		if($rowarticle["next_arti"])
		{
			$rowarticle["next_article_url"] = 'article-'.$rowarticle["next_arti"]["arid"].'-0.html';
		}
	}else{ 
		if($rowarticle["pre_arti"])
		{
			$rowarticle["pre_article_url"] = 'article.php?id='.$rowarticle["pre_arti"]["arid"]; 
		}

		if($rowarticle["next_arti"])
		{
			$rowarticle["next_article_url"] = 'article.php?id='.$rowarticle["next_arti"]["arid"]; 
		}
	}
	$rowarticle["main_article_url"] = $nowcatname;

	//同分类文章
	if($acid) {  
	   $idtype ="acid"; 
	   $tmpid  = next_node_all($acid,$pre."articat",$idtype,true);
	   $strID  = ($tmpid)?$acid.$tmpid:$acid;
	   $rowarticle["arti_catstxt"] = articles_list('top',10,$strID,'');  
	} 

	//此文章的评论
	$table = $pre."arti_comms";
	$strWhere = ($Aconf['support'])?" states = 3 ":" states <> 1 ";
	$strWhere = ' WHERE '.$strWhere.' AND arid='.$arid.' AND domain_id='.$Aconf['domain_id'];
	$sql = "SELECT arcid,email,ip,name,descs,dateadd,support,against   FROM ".$table.$strWhere." ORDER BY top desc, arcid desc limit 3";
	$AsppAll = $oPub->select($sql);
	$n = 1;
	while( @list( $k, $v ) = @each( $AsppAll) ) {
		$AsppAll[$k]['dateadd'] = date("y-n-j-h:i", $v['dateadd']);
		$Aip = explode(".",$v['ip']);
		$AsppAll[$k]["ip"] = '第'.$n.'楼 IP:'.$Aip[0].'.'.$Aip[1].'.*';
		$AsppAll[$k]["nick"] = empty($v['name'])?'匿名':$v['name'];
		$AsppAll[$k]["descs"] =  clean_html($v[descs]); 
		$n ++ ;
	}
	$rowarticle['arti_comms'] = $AsppAll; 
	  
	//同分类图库 start 
	//$nowtimes = gmtime();
	//$tmptime =  mktime(date("H",$nowtimes)-1,date("i",$nowtimes),date("s",$nowtimes),date("m",$nowtimes ),date("d",$nowtimes ),date("Y",$nowtimes));
	//AND a.arti_date > $tmptime 
	$where = $Strstates." AND   a.domain_id = '".$Aconf['domain_id']."'";
	$sql = "SELECT a.arid,a.name,a.subname,a.otherurl ,b.thumb_url  
				   FROM ".$pre."artitxt as a,  ".$pre."arti_file as b 
				   WHERE ".$where." and a.arid=b.arid  and a.acid ='".$acid."' 
				   group by b.arid desc limit 8";
	$rowarticles = $oPub->select($sql);
	if(!$rowarticles){
		$sql = "SELECT a.arid,a.name,a.subname,a.otherurl ,b.thumb_url FROM ".$pre."artitxt as a,  ".$pre."arti_file as b 
			   WHERE ".$where." and a.arid=b.arid  
			   group by b.arid desc limit 8";
		$rowarticles = $oPub->select($sql);
	} 
	while( @list( $key, $val ) = @each( $rowarticles) ) {
		if($val['otherurl']) {
		 $rowarticles[$key]['article_url'] =$val['otherurl'];
		}else{
			if($Aconf['rewrite']){
				$rowarticles[$key]['article_url'] = 'article-'.$val['arid'].'-0.html';	 
			}else{
				$rowarticles[$key]['article_url'] = 'article.php?id='.$val['arid'];
			}
		}
		$rowarticles[$key]['subname'] = sub_str($rowarticles[$key]['subname'],8,false);
	}
	$rowarticle['thumbs'] = $rowarticles;
	//本文关键词 
	$sql   = "SELECT `keys` FROM `".$pre."arti_tag` WHERE `domain_id` ='".$Aconf['domain_id']."' 
	AND art_pro_type = 0 AND arid = $arid";
	$rowarticle['keys'] = $oPub->select($sql); 
	//关联调查
	$updatevtid ='';
	if($rowarticle['vtid'] > 0 ){ 
		$sql = "SELECT a.vtid,a.vt_name,a.vt_desc FROM ".$pre."vote_title AS a 
			WHERE a.states=0 AND a.is_show = 1 and a.vtid='".$rowarticle['vtid']."' AND	 a.domain_id = ".$Aconf['domain_id']." order by top desc,vtid desc limit 1";
		$row = $oPub->getRow($sql);
		$rowarticle["vote"]["vote_title"] = $row;
		if($row) {  
			$rowarticle["vote"]["for_vote_group"]= vote($row["vtid"]); 
		}else{
			$updatevtid = ',vtid=0 ';	
		} 
	} 



	$Ahome["article"] = $rowarticle;

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
} 
$smarty->display($Aconf["displayFile"], $cache_id);  
?>
