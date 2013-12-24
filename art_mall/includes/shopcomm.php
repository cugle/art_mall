<?php 
/* 文章列表 查询条件 */
$Aconf['header_title'] = $Aweb_url['pravail'][0]."|".$Aconf['web_title']; 
$Aconf['description']  = ''; $Aconf['keywords']     = ''; 
 
$sql = "SELECT * FROM ".$pre."pravail WHERE  praid = '".$praid."' AND domain_id=".$Aconf['domain_id']. " LIMIT 1";
$rowpra = $oPub->getRow($sql);
if(!$rowpra) {
   $strMessage = '此商家网站已关闭';
   echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='pravail.php';</script>";
   exit;
}
/* 所属地区country province city  */ 

$sql = "SELECT fid FROM ".$pre."citycat where ccid = ".$rowpra['ccid']." limit 1";
$fid= $oPub->getOne($sql); 
if($fid){
	$preCcid = pre_node_orders($fid,$pre."citycat","ccid");
	$preCcid = $preCcid.','.$rowpra['ccid'];
}else{
	$preCcid = $rowpra['ccid'];
}

$Accid = explode(",",$preCcid);
$ccidNum = count($Accid);
$strCcid = '';
while( @list( $k, $v ) = @each( $Accid) ) { 
	$sql = "SELECT name FROM ".$pre."citycat where ccid = ".$v." AND domain_id=".$Aconf['domain_id']." limit 1";
	$name = $oPub->getOne($sql); 
	$strCcid .= $name.'>';
} 
$rowpra['ccid'] = substr($strCcid,0,-1);
$rowpra['dateadd'] = date("y年n月j日",$rowpra['dateadd']);
	
if($Aconf['rewrite']){
	$rowpra['shop_url'] = 'shop-'.$rowpra['praid'].'.html';
	$rowpra['pravail_url'] = 'pravail-0.html';
	$rowpra['shop_a'] = 'shop_a-'.$rowpra['praid'].'-0.html';
	$rowpra['shop_h'] = 'shop_h-'.$rowpra['praid'].'-0.html';
	$rowpra['shop_c'] = 'shop_c-'.$rowpra['praid'].'-0.html';
}else{
	$rowpra['shop_url'] = 'shop.php?id='.$rowpra['praid'];
	$rowpra['pravail_url'] = 'pravail.php';
	$rowpra['shop_a'] = 'shop_a.php?id='.$rowpra['praid'];
	$rowpra['shop_h'] = 'shop_h.php?id='.$rowpra['praid'];
	$rowpra['shop_c'] = 'shop_c.php?id='.$rowpra['praid'];
} 
/* 联系方法 start */	
$Asets = explode("{|}",$rowpra['sets']); 
if(count($Asets))
foreach ($Asets AS $v) {
	$At = array();
	$At = explode("[|]",$v); 
	if($At[0] && $At[1]) { 
		switch($At[0]) {
			case 'zip': $rowpra['zip'] = $At[1]; break;
			case 'address': $rowpra['address'] = $At[1]; break;
			case 'shop_name': $rowpra['shop_name'] = $At[1]; break;
			case 'contact': $rowpra['contact'] = $At[1]; break;
			case 'phone': $rowpra['phone'] = $At[1]; break;
			case 'tel': $rowpra['tel'] = $At[1]; break;
			case 'email': $rowpra['email'] = $At[1]; break;
			case 'msn': $rowpra['msn'] = $At[1]; break;
			case 'qq': $rowpra['qq'] = '<B style="color:#FF0000">在线QQ:</B><A HREF="http://wpa.qq.com/msgrd?V=1&Uin='.$At[1].'&Site='.$rowpra['pra_name'].'&Menu=yes">'.$At[1].'</A>'; break;
		} 
	}
} 
/* 联系方法 end */

/* 推荐产品 start */
$db_table = $pre."pravail_producttxt";
$where = "states=0 and  praid='".$praid."' AND domain_id = '".$Aconf['domain_id']."'";
$sql = "SELECT prid,main_prid,name,shop_price,min_thumb, shop_thumb FROM ".$db_table. " WHERE  $where ORDER BY top DESC,dateadd DESC LIMIT 3";
$row = $oPub->select($sql);
if($row ) { 
	$db_table = $pre."pravail_productcat";
    foreach ($row AS $key=>$val) {
	   /* 查询是否为总站商品 */
	   if($val['main_prid'] > 0 ) {
	      /* 读取分公司价格 */
           $db_table = $pre."producttxt";
           $where = " prid = '".$val['main_prid']."' AND states=0 AND domain_id = '".$Aconf['domain_id']."'";
           $sql = "SELECT name,shop_price,min_thumb, shop_thumb FROM ".$db_table." AS a WHERE 1 AND ". $where.' LIMIT 1';
           $rowtmp = $oPub->getRow($sql);
		   $row[$key]['name'] = $rowtmp['name'];
		   $row[$key]['sub_name'] = sub_str($rowtmp['name'],6,false);
           $row[$key]['shop_price'] = $rowtmp['shop_price'];
		   $row[$key]['min_thumb'] = ($rowtmp['min_thumb'])?$rowtmp['min_thumb']:'images/command/no_imgs.png';
		   $row[$key]['shop_thumb'] = ($rowtmp['shop_thumb'])?$rowtmp['shop_thumb']:'images/command/no_imgs.png';
          /*取得本公司报价 */
	       $db_table = $pre."price_history";
	       $sql = "SELECT shop_price    
	           FROM ".$db_table." 
			   where  prid = '".$val['main_prid']."'
			   AND praid = '".$praid."'
			   AND domain_id = '".$Aconf['domain_id']."' 
			   ORDER BY  dateadd DESC 
			   LIMIT 1";			
           $shop_price = $oPub->getOne($sql);
	       $row[$key]['shop_price'] = ($shop_price)?$shop_price:$row[$key]['shop_price']; 
	   }

		if($Aconf['rewrite']){ 
			$row[$key]['shop_ht'] = 'shop_ht-'.$praid.'-'.$row[$key]['prid'].'.html';
		}else{ 
			$row[$key]['shop_ht'] = 'shop_ht.php?id='.$praid.'&prid='.$row[$key]['prid'];
		}  
       $row[$key]['dateadd']  = ($val['dateadd'])?date("m月d日", $val['dateadd']):''; 
    } 
}
$rowpra['pravail_producttxt'] = $row; unset($row);
//自定义分类 
$AnormAll = $oPub->select( "SELECT prapcid,name  FROM ".$pre."pravail_productcat where praid  = '".$praid."'  AND domain_id=".$Aconf['domain_id']." ORDER BY prapcid ASC"); 
while( @list( $key, $value ) = @each( $AnormAll) ) {
	$db_table = $pre."pravail_producttxt";
	$tmp = $oPub->getOne("SELECT COUNT(*) as count FROM ".$pre."pravail_producttxt where prapcid  = '".$value['prapcid']."'  AND domain_id='".$Aconf['domain_id']."'"); 
	$count = ($tmp)?$tmp:0;
	if($Aconf['rewrite']){ 
		$AnormAll[$key]['shop_h'] = 'shop_h-'.$praid.'a'.$value['prapcid'].'-0.html';
	}else{ 
		$AnormAll[$key]['shop_h'] = 'shop_h.php?id='.$praid.'a'.$value['prapcid'];
	} 
	$AnormAll[$key]['count'] = $count; 
} 
$rowpra['productcat'] =  $AnormAll; 
/* 促销信息 */ 
$AnormAll = $oPub->select("SELECT arid,name  FROM ".$pre."pravail_artitxt where praid  = '".$praid."' AND domain_id=".$Aconf['domain_id']."  AND states <> 1  ORDER BY dateadd DESC limit 20");  
while( @list( $key, $value ) = @each( $AnormAll) )
{
	if($Aconf['rewrite']){ 
		$AnormAll[$key]['shop_a'] = 'shop_a-'.$praid.'-'.$value['arid'].'.html';
	}else{ 
		$AnormAll[$key]['shop_a'] = 'shop_a.php?id='.$praid.'&arid='.$value['arid'];
	}  
}
$rowpra['pravail_artitxt'] = $AnormAll;unset($AnormAll);
?> 