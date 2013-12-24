<?php
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."ads.php"); 

if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}
//重新构造当前URL 
$sPriStar = $s<0.01?0:$s;
$sPriEnd  = $e<0.01?0:$e;
$bys      = $bys < 1?0:$bys;
$A["id"]   = $id    < 1?0: $id;
$A["page"] = $page  < 1?0: $page;  
if($Aconf['rewrite']){
	$Ahome["jsnow_url"]= $Aconf["domain_url"].$Aconf["preFile"]."-".$A["id"];
	$Ahome["jsnow_page"] = $A["page"];
}else{
	$Ahome["jsnow_url"]= PHP_SELF."?id=".$A["id"]."&page=".$A["page"]; 
}
 
/* 搜索查询不缓存
 *bys
 *sPri_star  SPri_end
*/
if($bys > 0 || !empty($pse)){
	$cache_id =''; 
	$Ahome["bys"] = $bys; 
}else{ 
	$strCache_id = $Aconf['domain_id'].$id.$page; 
	$cache_id = sprintf('%X', crc32($str_cache_id));
}
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php");
	$Aconf['header_title'] = $Aweb_url['products'][0]."|".$Aconf["web_title"]; 
	$pcid = $id + 0; 
	$Ahome["pcid"] = $pcid ;
	/* 得到当前分类的所有下级分类 start */ 
	$tmpid    = next_node_all($id,$pre."productcat","pcid",true);
	$strID    = ($tmpid)?$id.','.$tmpid:$id;  
	 /* 分类翻页 */ 
	$strTmp = ($id > 0)?' AND  pcid in('.$strID.') ':' ';
	$where = '  states<>1 AND   domain_id = "'.$Aconf['domain_id'].'"'.$strTmp; 
	//搜索初始值
	$whereExt = '';
	if($sPriStar > 0){
		$where    .= ' and  shop_price>= '.$sPriStar;
		$whereExt .= ' and shop_price>= '.$sPriStar;
		$Ahome['sPriStar'] = $sPriStar ; 
	}else{
		$Ahome['sPriStar'] ='';
	}

	if($sPriEnd > 0){
		$where    .= ' and  shop_price < '.$sPriEnd;
		$whereExt .= ' and shop_price < '.$sPriEnd;
		$Ahome["sPriEnd"] = $sPriEnd ; 
	}else{
		$Ahome["sPriEnd"] ='';
	} 
	$Ahome['products_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'producttxt WHERE 1 AND '. $where);  
	$page = new ShowPage;
	$page->PageSize = $Aconf['set_pagenum'];
	$page->PHP_SELF = PHP_SELF;
	$page->Total = $Ahome['products_count'];
	$pagenew = $page->PageNum();
	$page->LinkAry = array('id'=>$pcid,'s'=>$sPriStar,'e'=>$sPriEnd,'bys'=>$bys); 
	$strOffSet = $page->OffSet(); 
	$Ahome['showpage'] = ($Ahome['products_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
	//排序
	$bys = $bys == 6?'hots asc':($bys==5?'hots desc':($bys==4?'shop_price asc':($bys==3?'shop_price desc':($bys==2?'up_date asc':'prid desc'))));
	$Ahome["products"] = products_list( $bys,"$strOffSet",$strID,$whereExt);   
	/* 得到所有产品分类的关联文章分类 */
	if(!empty($strID)){
		$row = $oPub->select('SELECT acids FROM '.$pre.'productcat  where pcid in ('.$strID.') and domain_id="'.$Aconf['domain_id'].'"'); 
		$stracids = '';
		while( @list( $k, $v) = @each( $row ) ) {
			$stracids .= $v['acids'].','; 
		}
		if(!empty($stracids)){
			$Astracids = explode(",",$stracids);
			$Aacids = array();
			while( @list( $k, $v) = @each( $Astracids ) ) {
				if($v > 0 && !in_array($v,$Aacids)){
					array_push($Aacids,$v); 
				} 
			}
			//找到当前文章分类的子分类 
			$idtype ='acid';
			$stracids = '';
			while( @list( $k, $v) = @each( $Aacids ) ) {
				$tmpid  = next_node_all($v,$pre.'articat',$idtype,true);
				$strID  = ($tmpid)?$v.$tmpid:$v;
				$stracids .= $strID.',';
			}
			$Astracids = explode(",",$stracids);
			$Aacids = array();
			while( @list( $k, $v) = @each( $Astracids ) ) {
				if($v > 0 && !in_array($v,$Aacids)){
					array_push($Aacids,$v); 
				} 
			}
			$stracids = implode(",", $Aacids); //所有关联文章分类
			$Ahome['pro_articles'] = articles_list( 'top', 20,$stracids,26);
		}
	}else{
		$Ahome['pro_articles'] = articles_list( 'top', 20,'',26);
	}
	 /* 得到所有关联文章分类 end */ 
	/* 当前分类的所有前置分类 用于当前位置导航 */ 
	$row1 = $oPub->getRow('SELECT name,fid,descs,keywords FROM '.$pre.'productcat 
		where pcid = "'.$pcid.'" and domain_id="'.$Aconf['domain_id'].'" limit 1'); 
	$nowcatname = $row1['name']; 
	$Aconf['description']  = $row1["descs"];
	$Aconf['keywords']     = $row1["keywords"]; 
	$fid1 = $row1['fid'];
	$Ahome["pro_fid1"] = $fid1;
	$strPrenave = pre_node($fid,$pre."productcat","pcid",$Aconf["nowFile"],true);
	$tmpnowNave = empty($nowcatname)?'':'<li>'.$nowcatname.' '.$Aconf['nav_symbol'].'</li>';

	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li> <A HREF="'.$Aconf["nowFile"].'">'.$Aweb_url[$Aconf["preFile"]][0].'</a> '.$Aconf['nav_symbol'].'</li>'.$strPrenave.$tmpnowNave; 
	$Aconf["header_title"] = $nowcatname.'|'.$Aconf['header_title']; 
	 //关联品牌 start
	if($pcid > 0){ 
		 $topid = ($fid >0)? pre_node_top($fid,$pre."productcat","pcid"):$pcid;  
		 $row = $oPub->select('SELECT a.counts,b.prbid,b.brand_name   FROM '.$pre.'probrand_procat as a,'.$pre.'probrand as b where a.pcid="'.$topid.'" and b.prbid=a.prbid group by b.prbid order by b.sort_order asc'); 
	 }else{
		 $row = $oPub->select('SELECT a.counts,b.prbid,b.brand_name   FROM '.$pre.'probrand_procat as a,'.$pre.'probrand as b where b.prbid=a.prbid and b.domain_id="'.$Aconf['domain_id'].'" group by b.prbid  order by b.sort_order asc');
	 }
    if($row)
    foreach ($row AS $key=>$value) {
		$row[$key]["brand_logo"] = ($value["brand_logo"] == '')?'images/command/no_brandimgs.png':'data/brandlogo/'.$value["brand_logo"];	
		if($Aconf['rewrite']){
			$row[$key]['brand_url'] = 'brand-'.$value["prbid"].'-0.html'; 
		}else{
			$row[$key]['brand_url'] = 'brand.php?id='.$value["prbid"]; 
		} 	   
    }
	$Ahome["pro_brands"] = $row; 
	 //关联品牌 end 
	 //当前分类的平级分类,及下级分类 start
	if($fid > 0){
		$row = $oPub->select('SELECT pcid,name,next_node FROM '.$pre.'productcat where fid = "'.$fid.'" ORDER BY pcid ASC'); 
		while( @list( $key, $val ) = @each( $row ) ) {
			$row[$key]['name'] = $val["name"];
			if($Aconf['rewrite']){
					$row[$key]['name_url'] = 'products-'.$val["pcid"].'-0-0-0-0.html';
			}else{
					$row[$key]['name_url'] = 'products.php?id='.$val["pcid"]; 
			}
			//得到下级分类
			$row[$key]["next_node"] = '';
			$tmpid  = next_node_all($val["pcid"], $pre."productcat","pcid",false);
			if($tmpid){
				$Atmp = explode(",",$tmpid);
				$tmpid = '';
				while( @list( $keyx, $valx ) = @each( $Atmp ) ) {
					if($valx > 0 ){
						$tmpid  .=$valx.',';
					}
				}
				if($tmpid){
					$tmpid = substr($tmpid,0,-1);
				} 
			} 
			if($tmpid){ 
				$rownext = $oPub->select('SELECT pcid,name FROM '.$pre.'productcat where pcid in('.$tmpid.') AND domain_id="'.$Aconf['domain_id'].'" ORDER BY pcid ASC'); 
				$str = '';
				while( @list( $keyy, $valy ) = @each( $rownext ) ) {
					if($Aconf['rewrite']){
						$str = '<a href="products-'.$valy["pcid"].'-0-0-0-0.html">'.$valy["name"].'</a>';
					}else{
						$str= '<a href="products.php?id='.$valy["pcid"].'">'.$valy["name"].'</a>';
					}
					$rownext[$keyy]["sub_next"] = $str;
				}
				$row[$key]["next_node"] = $rownext; 
			}

		}
		$Ahome["productcat"] = $row;
	}//$fid > 0
	 //当前分类的平级分类 end 
    assign_template($Aconf); 
    $smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);  

?>
