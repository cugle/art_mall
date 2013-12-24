<?php
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
include_once( ROOT_PATH."ads.php");

$prid = $id +0;  
/* 访问量 +1 */ 
if (!is_spider())
{
	$oPub->query("UPDATE ". $pre."producttxt SET hots = hots + 1  WHERE prid = '".$prid."' limit 1");  
	/* 最近浏览 */
	$userid  = $_SESSION['user_id']?$_SESSION['user_id']:0; 
	$oPub->query('DELETE FROM ' . $pre.'sesspro WHERE prid="'.$prid.'" and sesskey="'.SESS_ID.'"');
	$oPub->query('INSERT INTO ' . $pre.'sesspro (sesskey,expiry,userid,prid,domain_id) VALUES ("'.SESS_ID.'", "'. time() .'","'.$userid.'","'. $prid .'","'.$Aconf['domain_id'].'")'); 
}

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$prid));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php"); 
	$rowproduct = $oPub->getRow("SELECT a.prid,a.pcid,a.pacid,a.prbid,a.name,a.enname,a.cnwidth,a.cnheight,a.enwidth,a.enheight,a.edit_comm,a.praids,a.shop_sn,a.shop_price,a.shop_number,a.s_discount,a.s_dis_exp,a.dateadd,a.up_date,a.comms,a.hots,a.shop_thumb,a.filename,a.mis_thumb,a.states,b.descs,b.cltion,b.cltion_product,b.cltion_topic,b.file_exp FROM ".$pre."product as b,".$pre."producttxt as a
			WHERE  b.prid = $prid and  a.states <> 1 and a.prid = b.prid and a.domain_id=".$Aconf['domain_id']. " LIMIT 1"); 
	if(!$rowproduct) {
		$strMessage = '此产品已不存在！';
		/* 删除对应的图片 */
		$db_table = $pre.'product_file';
		$sql = "SELECT fileid,filename,thumb_url  FROM " . $db_table . " WHERE prid = '$prid' and domain_id='".$Aconf['domain_id']."'";
		$row = $oPub->select($sql);
		if($row)
		foreach ($row AS $key=>$value) {
			if ($value['thumb_url'] != '' && is_file('../' . $value['thumb_url'])) {
				@unlink('../' . $value['thumb_url']);
			}
			if ($value['filename'] != '' && is_file('../' . $value['filename'])) {
				@unlink('../' . $value['filename']);
			}
			/* 删除数据 */
			$sql = "DELETE FROM " . $db_table . " WHERE fileid = '".$value[fileid]."' and domain_id='".$Aconf['domain_id']."'  LIMIT 1";
			$oPub->query($sql);
		}
		echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='products.php';</script>";
		exit;
	}

	/* 产品页显示的模块 */   
	$Aconf['header_title'] = $Aweb_url['products'][0]."|".$Aconf["web_title"]; 
	$Aconf['description']  =  $Aconf['keywords']     = ''; 

	//上一商品 下一商品
	$pre_row = $oPub->getRow("SELECT prid,name  FROM  ".$pre."producttxt  WHERE  states <> 1   AND domain_id=".$Aconf['domain_id'].
		" and prid > ".$rowproduct[prid]." order by prid asc  LIMIT 1");  
	$rowproduct["pre_name"] = $pre_row["name"];
	$rowproduct["pre_name_prid"] = $pre_row["prid"];
	$next_row = $oPub->getRow("SELECT prid,name  FROM  ".$pre."producttxt  WHERE  states <> 1   AND domain_id=".$Aconf['domain_id'].
		" and prid < ".$rowproduct[prid]." order by prid desc  LIMIT 1");
	$rowproduct["next_name"] = $next_row["name"];	
	$rowproduct["next_name_prid"] = $next_row["prid"];	
	if($Aconf['rewrite']){
		$rowproduct["pre_url"] = "product-".$pre_row["prid"].".html"; 
		$rowproduct["next_url"] = "product-".$next_row["prid"].".html";  
	}else{ 
		$rowproduct["pre_url"] = "product.php?id=".$pre_row["prid"]; 
		$rowproduct["next_url"] = "product.php?id=".$next_row["prid"]; 
	}

	//产品详细资料 
	$rowproduct['dateadd']  = ($rowproduct['dateadd'])?date("y年m月d日h:i", $rowproduct['dateadd']):'';
	$rowproduct['up_date']  = ($rowproduct['up_date'])?date("Y-m-d", $rowproduct['up_date']):'';
	//品牌
	if($rowproduct['prbid']) {
	   $sql = "SELECT  prbid,brand_name,brand_logo,brand_desc,site_url FROM ".$pre."probrand  
			where prbid = '".$rowproduct['prbid']."' AND domain_id = ".$Aconf['domain_id']." LIMIT 1";
	   $row = $oPub->getRow($sql);
	   $row['brand_logo'] = ($row['brand_logo'])?'data/brandlogo/'.$row['brand_logo']:'';
	   if($Aconf['rewrite']){
			$row['brand_url'] = 'brand-'.$row['prbid'].'-0.html';
	   }else{
			$row['brand_url'] = 'brand.php?id='.$row['prbid'];
	   }
	   $rowproduct['probrand'] = $row;unset($row); 
	}
	
	//同品牌下的产品aabycugle
		$prbid=$rowproduct['prbid'];
		$where = " a.states=0 AND a.prbid='".$prbid."' AND  a.domain_id = '".$Aconf['domain_id']."'";
		$sqlbrand = "SELECT a.prid,a.pcid,a.name,a.shop_price,a.s_discount,a.s_dis_exp,a.colors,a.up_date,a.dateadd,a.comms,a.min_thumb,a.shop_thumb       
		FROM ".$pre."producttxt as a,".$pre."product as b   
		WHERE  $where 
		AND a.prid = b.prid 
		ORDER BY a.top DESC,up_date desc  
		LIMIT 16";
		
		$sqlbrandcount = "SELECT COUNT(*) as count FROM ".$pre."producttxt  AS a WHERE 1 AND ". $where;
		
		$rowproduct["count"] = $oPub->getOne($sqlbrandcount); 
		$rowbrandproduct = $oPub->select($sqlbrand); 
	
	if($rowbrandproduct ){ 
		foreach ($rowbrandproduct AS $key=>$val) { 
			if($val['colors']){
				$rowbrandproduct[$key]['name'] =  '<span style="color:'.$val['colors'].'">'.$val[name].'</span>'; 
			} 
			if(!$val['min_thumb']){
				$rowbrandproduct[$key]['min_thumb'] = 'images/command/no_imgs.png';
			}

			if(!$val['shop_thumb']){
				$rowbrandproduct[$key]['shop_thumb'] = 'images/command/no_imgsbig.png';
			}	
			
			$rowbrandproduct[$key]['shop_price'] = ($val[shop_price] == '0.00')?'':$val[shop_price];
			$rowbrandproduct[$key]['s_discount'] = ($val[s_discount] == '0.00')?'':$val[s_discount];
			$rowbrandproduct[$key]['dateadd']  = ($val['dateadd'])?date("Y年m月d日h:i", $val['dateadd']):'';
			$rowbrandproduct[$key]["up_date"]  = ($val["up_date"] > 0)?date("y年n月j日",$val["up_date"]):'dddddddd';  
			$sql = "SELECT name FROM ".$pre."productcat WHERE pcid=$val[pcid]";
			$row = $oPub->getRow($sql);
			$rowbrandproduct[$key]['pcname'] = $row[name]; 

			if($Aconf['rewrite']){
				$rowbrandproduct[$key]['product_url'] = 'product-'.$val[prid].'.html';
				$rowbrandproduct[$key]['pcomms_url'] = 'product_comms-'.$val[prid].'.html';
				$rowbrandproduct[$key]['pcname_url'] = 'products-'.$val[pcid].'-0.html';
			}else{
				$rowbrandproduct[$key]['product_url'] = 'product.php?id='.$val[prid];
				$rowbrandproduct[$key]['pcomms_url'] = 'product_comms.php?id='.$val[prid];
				$rowbrandproduct[$key]['pcname_url'] = 'products.php?id='.$val[pcid];
			} 
		}
	} 
	
	$rowproduct["brandproduct"] = $rowbrandproduct;unset($rowbrandproduct); 
	$Ahome["brandproduct"] = $rowproduct;
	unset($rowprobrand);
	
//end同品牌下的产品
	if($Aconf['rewrite']){
		$rowproduct['comms_url'] = 'procomms-'.$rowproduct['prid'].'-0.html';
		$rowproduct['images_list_url'] = 'images_list-'.$rowproduct['prid'].'-1.html';
	}else{
		$rowproduct['comms_url'] = 'procomms.php?id='.$rowproduct['prid'];
		$rowproduct['images_list_url'] = 'images_list.php?id='.$rowproduct['prid'].'&op=1';
	}
	/* 经销商 satr*/
	if($rowproduct['praids'] != '') {
		$Apraids =  explode(",",$rowproduct['praids']);  
		foreach($Apraids as $k => $v) {
			if($v){
				$row = $oPub->getRow("SELECT *  FROM ".$pre."pravail where  domain_id=".$Aconf['domain_id']." and praid='".$v."'"); 

				if($Aconf['rewrite']){
					$ApravailAll[$k]['pra_name_url'] = 'shop-'.$row["praid"].'.html';
				}else{
					$ApravailAll[$k]['pra_name_url'] = 'shop.php?id='.$row["praid"];
				} 
				$ApravailAll[$k]['praid'] = $row[praid];
				$ApravailAll[$k]['pra_name'] = $row[pra_name];
				$ApravailAll[$k]['pra_url'] = $row[pra_url];
				/* 联系方法 start */	
				$Asets = explode("{|}",$row['sets']); 
				if(count($Asets))
				foreach ($Asets AS $v) {
					$At = array();
					$At = explode("[|]",$v); 
					if($At[0] && $At[1]) { 
						$ApravailAll[$k][$At[0]] = $At[1];
					}
				}
				/* 联系方法 end */

				/* 经销商是否有历史价格查询 */ 
				$rowph = $oPub->getRow("SELECT prhid,shop_price FROM ".$pre."price_history where  prid = '".$rowproduct['prid']."' AND praid = '".$row['praid']."' AND domain_id = '".$Aconf['domain_id']."' 
					ORDER BY dateadd DESC  LIMIT 1"); 
				$ApravailAll[$k]['price_history'] = ($rowph['prhid'] > 0 ) ? $rowph['shop_price']:false;
			}
		 } 
		 $rowproduct['pravail'] = $ApravailAll;
	} 
	/* 关联文章 */
	if($rowproduct['cltion'] != '') {
		$strCltion = '';
		$Acltion = explode("{|}",$rowproduct['cltion']);
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
			 $rowproduct['cltion'] =$AstrCltion;
		} else {
			 $rowproduct['cltion'] = false;
		} 
	}
	/* 关联产品 */
	if($rowproduct['cltion_product'] != '') { 
		$strCltion = '';
		$Acltion = explode("{|}",$rowproduct['cltion_product']);
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
			 $rowproduct['cltion_product'] =$AstrCltion;
		} else {
			 $rowproduct['cltion_product'] = false;
		}  
	}

	/* 像册列表 */ 
	$rowproduct["img_list"] = $oPub->select("SELECT thumb_url,shop_thumb,filename,descs FROM " . $pre."product_file WHERE prid = $rowproduct[prid] limit 4"); 
	if($rowproduct["img_list"]) {
		$rowproduct["show_img_list"] = true;
	}
	/* 产品属性 start */ 
	 $row = $oPub->select("SELECT paid,pacid,attr_name  FROM ".$pre."prattri  WHERE `pacid` = $rowproduct[pacid] ORDER BY sort_order,paid ASC");  
	 while( @list( $k, $v) = @each( $row) ) {
		/* 取对应值 */ 
		$row[$k][pavals] = $oPub->getOne("SELECT pavals  FROM ".$pre."prattrival where `paid` = $v[paid] AND prid  = $rowproduct[prid] limit 1");  
	 }
	 $rowproduct[prattri] = $row;unset($row); 
	 /* 是否有历史价格查询 */
	 /*
	 $db_table = $pre."price_history";
	 $sql = "SELECT prhid FROM ".$db_table." where  prid = '".$rowproduct['prid']."' AND domain_id = '".$Aconf['domain_id']."'  LIMIT 1";
	 $rowph = $oPub->getRow($sql);
	 $rowproduct['prid_prhistory'] = ($rowph['prhid'] > 0 ) ? $rowproduct['prid']:false; 
	 */
	//商品品论
	$table = $pre."product_comms";
	$strWhere = ($Aconf['support'])?" states = 3 ":" states <> 1 ";
	$strWhere = ' WHERE '.$strWhere.' AND prid="'.$prid.'" AND domain_id='.$Aconf['domain_id'];
	$sql = "SELECT *   FROM ".$table.$strWhere." ORDER BY prcid desc limit 50";
	$AsppAll = $oPub->select($sql);
	$n = 1;
	while( @list( $k, $v ) = @each( $AsppAll) ) {
		$AsppAll[$k][dateadd] = date("y年n月j日h:i", $v[dateadd]);
		$Aip = explode(".",$v[ip]);
		$AsppAll[$k]["ip"]     = 'IP:'.$Aip[0].'.'.$Aip[1].'.*';
		$AsppAll[$k]["name"]   = $v[name];
		$AsppAll[$k]["email"]  = $v[email];
		$AsppAll[$k]["descs"]  =  clean_html($v[descs]); 
		$n ++ ;
	}
	$rowproduct["pro_comms"] = $AsppAll;
	//产品详细信息结束
	$Ahome["product"] = $rowproduct;

	/* 得到所有产品分类的关联文章分类 */
	$strID=$rowproduct["pcid"];
	if(!empty($strID)){
		$sql = "SELECT acids FROM ".$pre."productcat  where pcid in (".$strID.") and domain_id=".$Aconf['domain_id'];
		$row = $oPub->select($sql); 
		$stracids = '';
		while( @list( $k, $v) = @each( $row ) ) {
			$stracids .= $v["acids"].','; 
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
			
			$idtype ="acid";
			$stracids = '';
			while( @list( $k, $v) = @each( $Aacids ) ) {
				$tmpid  = next_node_all($v,$pre."articat",$idtype,true);
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
			$Ahome["pro_articles"] = articles_list( 'top', 20,$stracids,26);
		}
	}else{
		$Ahome["pro_articles"] = articles_list( 'top', 20,'',26);
	}
	/* 得到所有关联文章分类 end */
	//得到同分类商品 start 
	$Ahome['pro_pcid'] = products_list('up_date desc ', 3,$rowproduct["pcid"]); 

	/* 当前位置导航 */ 
	$sql = "SELECT name,descs,fid,ifnav FROM ".$pre."productcat 
		where  pcid = '".$rowproduct["pcid"]."'
		AND domain_id=".$Aconf['domain_id']." 
		LIMIT 1";
	$row = $oPub->getRow($sql); 
	$fid = $row[fid];  
	if($Aconf['rewrite']){
		$nowcatname =  '<a href="products-'.$rowproduct[pcid].'-0-0-0-0.html">'.$row[name].'</a> '.$Aconf['nav_symbol']; 
		$products = 'products.html';
	}else{
		$nowcatname =  '<a href="products.php?id='.$rowproduct[pcid].'">'.$row[name].'</a> '.$Aconf['nav_symbol'];
		$products = 'products.php';
	}
	$idtype = "pcid";
	$strPrenave = pre_node($fid,$pre."productcat",$idtype,$products,true);
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li><A HREF="'.$products.'">'.$Aweb_url["products"][0].'</a> '.$Aconf['nav_symbol'].'</li>'.$strPrenave.' <li>'.$nowcatname.'</li><li><span style="font-weight:lighter"> 详情</span></li>'; 
	$Aconf["header_title"] = $rowproduct[name].'|'.$row[name].'|'.$Aconf['header_title'];  
	$Aconf["product_catname"] = $row[name];  
	$Aconf["product_encatname"] = $row[descs];  
	unset($rowproduct);
	/* 当前位置导航负值结束 */

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?> 
