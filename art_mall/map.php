<?php
define('IN_OUN', true);
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."includes/item_set.php");  
$Aconf['header_title'] = $Aweb_url['map'][0]."|".$Aconf['web_title'];  
//查找所有的顶级分类

if($id > 0){
	$idold = $id;
	$n  = strlen($id); 
	$id = substr($idold,0,$n-1); //$id 分类ID
	$t  = substr($idold,$n-1,$n); //$t 为1/2 :网页形式/xml形式
	$acid = $id;
 
	$nowcatname = $oPub->getOne('SELECT name FROM '.$pre.'articat where  acid = "'.$acid.'" AND domain_id="'.$Aconf['domain_id'].'" LIMIT 1');   
	$idtype ="acid"; 
	
	$tmpid  = next_node_all($id,$pre."articat",$idtype,true);
	$strID  = ($tmpid)?$acid.$tmpid:$acid; 
	$acids=  " AND a.acid in($strID) ";  
	$where = " a.states <> 1 AND  a.domain_id = ".$Aconf['domain_id'].$acids; 
 
	$orderby = 'arti_date';$acid = $strID;$substr=30;
	$rowarticles = articles_list( $orderby, 100,$acid,$substr);  
	//输出xml格式文件
	$rssstr ='<rss version="2.0">';
	$rssstr .='<channel>'; 
	$rssstr .='<title>'.$nowcatname.'</title>';  
	$rssstr .='<description>'.$Aconf['header_title'].$nowcatname.'</description>'; 
	$rssstr .='<link>'.$Aconf['domain_url'].'</link>'; 
	$rssstr .='<copyright>Copyright 2010 - 2015 '.$Aconf['footer_title'].'. All Rights Reserved</copyright>'; 
	$rssstr .='<language>utf-8</language>'; 
	$rssstr .=' <generator>'.$Aconf['domain_url'].'</generator>';
	while( @list( $key, $val ) = @each( $rowarticles ) ) {
		$rssstr .='<item>'; 
		$rssstr .='<title><![CDATA['.$val['name'].']]></title>'; 
		$rssstr .='<link>'.$Aconf['domain_url'].$val['article_url'].'</link>'; 
		$rssstr .='<author>'.$Aconf['domain_url'].'</author>'; 
		$rssstr .='<category/>'; 
		$rssstr .='<pubDate>'.$val['arti_date'].'</pubDate>'; 
		$rssstr .='<comments/>'; 
		if(!empty($val['edit_comm'])){
			$rssstr .='<description><![CDATA['.$val['edit_comm'].']]></description>'; 
		}else{
			$sql = 'SELECT  b.descs  FROM '.$pre.'article as b WHERE  b.arid  ="'.$val['arid'].'" LIMIT 1'; 
			$descs = $oPub->getOne($sql);
			$rssstr .='<description><![CDATA['.sub_str(clean_html($descs),100).']]></description>';
		}
		$rssstr .='</item>'; 
	}
	$rssstr .='</channel>';
	$rssstr .='</rss>';
	header('Content-Type: text/xml');
	echo  "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";  
	echo $rssstr;
	exit;

 
 
}else{
	$sql = 'SELECT acid,name,descs FROM '.$pre.'articat  where fid=0   AND domain_id="'.$Aconf['domain_id'].'" ORDER BY acid ASC';
	$rowarticles = $oPub->select($sql); 
	$str = '';
	while( @list( $key, $val ) = @each( $rowarticles ) ) {
		if($Aconf['rewrite']){
			$rowarticles[$key]['acname_url'] = 'map-'.$val['acid'].'1-1.html';
			$rowarticles[$key]['acname_xmlurl'] = 'map-'.$val['acid'].'2-2.html'; 
		}else{
			$rowarticles[$key]['acname_url'] = 'map.php?id='.$val['acid'].'1'; 
			$rowarticles[$key]['acname_xmlurl'] = 'map.php?id='.$val['acid'].'2';
		} 
	} 
	$Ahome['map'] = $rowarticles; unset($rowarticles); 
	$Ahome['nowNave']  = '<li><A HREF="./">'.$Aweb_url['index'][0].'</A> '.$Aconf['nav_symbol'].'</li><li><A HREF="map.php">'.$Aweb_url['map'][0].'</a></li>'; 
}
$Ahome['map_type'] = $t;
 

if ((DEBUG_MODE & 2) != 2){
    $smarty->caching = true;
}
/* 调用模板 */ 
$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$id.$t));
if (!$smarty->is_cached($Aconf['displayFile'], $cache_id)) {
	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf['displayFile'], $cache_id); 
?>
