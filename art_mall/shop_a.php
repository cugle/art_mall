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

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$prid));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php"); 
	include_once( ROOT_PATH."includes/shopcomm.php");   
	/* 促销信列表 */ 
	$AnormAll = $oPub->select("SELECT arid,name  FROM ".$pre."pravail_artitxt where praid  = '".$praid."' AND domain_id=".$Aconf['domain_id']." AND states <> 1  ORDER BY dateadd DESC limit 50"); 
  	while( @list( $key, $value ) = @each( $AnormAll) )
	{
		if($Aconf['rewrite']){ 
			$AnormAll[$key]['shop_a'] = 'shop_a-'.$praid.'-'.$value[arid].'.html';
		}else{ 
			$AnormAll[$key]['shop_a'] = 'shop_a.php?id='.$praid.'&arid='.$value[arid];
		}  
	}
	$rowpra['pravail_artitxt'] = $AnormAll; 
	//促销信息记录
	if($arid > 0 ){
		  $sql = "SELECT b.arid,b.name,b.descs,b.dateadd FROM ".$pre."pravail_article as b,".$pre."pravail_artitxt as a
			WHERE  b.arid = $arid AND  b.states <> 1  AND a.arid = b.arid AND a.praid = $praid  AND b.domain_id=".$Aconf['domain_id']. " LIMIT 1";
	}else{
		  $sql = "SELECT b.arid,b.name,b.descs,b.dateadd FROM ".$pre."pravail_article as b,".$pre."pravail_artitxt as a
			WHERE   b.states <> 1 AND a.arid = b.arid AND a.praid = $praid  AND b.domain_id=".$Aconf['domain_id']. " order by b.dateadd desc LIMIT 1";
	}
	$rowarticle = $oPub->getRow($sql);
	if(!$rowarticle){
		$rowarticle = false;
	}else{
		$arid = $rowarticle[arid];
		/* 取相册 */  
		$sql = "SELECT thumb_url,filename,descs FROM " .$pre."pravail_arti_file WHERE arid = $arid";
		$rowarticle[img_list] = $oPub->select($sql);
		if($rowarticle[img_list]) {
			$rowarticle[show_img_list] = true;
		} 
		/* 取得上一条下一条记录 */
		$pre_next_art = ''; 
		$sql = "SELECT arid,name  FROM ".$pre."pravail_artitxt 
			  where arid > '".$arid."'   AND praid = '".$praid."' AND states <> 1 order by arid asc limit 1";
		$row = $oPub->getRow($sql);
		if($row[arid] > 0 ) {
			if($Aconf['rewrite']){
				$rowarticle['pre_art'] = '<a href="shop_a-'.$praid.'-'.$row[arid].'.html" title='.$row[name].'>'.sub_str($row[name],12,false).'</a>';
			}else{
				$rowarticle['pre_art'] = '<A HREF="shop_a.php?id='.$praid.'&arid='.$row[arid].'" title='.$row[name].'>'.sub_str($row[name],12,false).'</A>';
			} 
		}  
		$sql = "SELECT arid,name  FROM ".$pre."pravail_artitxt 
			  where arid < '".$arid."' 
			  AND praid = '".$praid."'
			  AND states <> 1
			  order by arid DESC limit 1";
		$row = $oPub->getRow($sql);
		if($row[arid] > 0 ) { 
			if($Aconf['rewrite']){
				$rowarticle['next_art'] = '<a href="shop_a-'.$praid.'-'.$row[arid].'.html" title='.$row[name].'>'.sub_str($row[name],12,false).'</a>';
			}else{
				$rowarticle['next_art'] = '<A HREF="shop_a.php?id='.$praid.'&arid='.$row[arid].'" title='.$row[name].'>'.sub_str($row[name],12,false).'</A>';
			}
		}  
	}
	$Ahome["nowNave"]  = '<li><a href="'.$rowpra['shop_url'].'">'.$rowpra[pra_name].'</a></li><li>'.$rowarticle['name'].'</li>'; 
	$Aconf["header_title"] = $rowarticle['name'].'|'.$rowpra[pra_name].'|'.$Aconf['header_title']; 
	$rowpra["pravail_article"] = $rowarticle; unset($rowarticle); 
	$Ahome["pravail"] = $rowpra; unset($rowpra); 

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?> 