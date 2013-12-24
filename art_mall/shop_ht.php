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
 
 
$oPub->query("UPDATE " . $pre."pravail_producttxt SET hots= hots + 1  WHERE `prid` ='".$prid."' and `domain_id`='".$Aconf['domain_id']."'"); 

if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$prid));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {
	include_once( ROOT_PATH."includes/item_set.php"); 
	include_once( ROOT_PATH."includes/shopcomm.php"); 
	$row = $oPub->getRow("select  prid, main_prid,prapcid,comms,hots FROM ".$pre."pravail_producttxt where prid = '".$prid."' limit 1"); 
	if(!$row) {
		$strMessage = '此产品已不存在！';
		/* 删除对应的图片 */ 
		$sql = "SELECT fileid,filename,thumb_url  FROM " . $pre."pravail_product_file WHERE prid = '$prid' and domain_id='".$Aconf['domain_id']."'";
		$rowf = $oPub->select($sql);
		if($rowf)
		foreach ($rowf AS $key=>$value) {
			if ($value['thumb_url'] != '' && is_file('../' . $value['thumb_url'])) {
				@unlink('../' . $value['thumb_url']);
			}
			if ($value['filename'] != '' && is_file('../' . $value['filename'])) {
				@unlink('../' . $value['filename']);
			} 
			/* 删除数据 */
			$sql = "DELETE FROM " . $pre."pravail_product_file WHERE fileid = '$value[fileid]' and domain_id='".$Aconf['domain_id']."'  LIMIT 1";
			$oPub->query($sql);
		}
		echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='".$rowpra['shop_h']."';</script>";
		exit;
	} 
	if($row[main_prid] > 0 ) {
		$sql = "SELECT a.prid,a.pacid,a.shop_sn,a.prbid,a.name,a.shop_price,a.up_date,a.dateadd,a.shop_thumb,b.descs FROM ".$pre."product as b,".$pre."producttxt as a
		WHERE  b.prid = '".$row[main_prid]."' AND  a.states <> 1  AND a.prid = b.prid AND a.domain_id=".$Aconf['domain_id']. " LIMIT 1";
		$rowproduct = $oPub->getRow($sql);
		$rowproduct[comms] = $row[comms];
		$rowproduct[hots] = $row[hots];
	} else
	{
		$rowproduct = $oPub->getRow("SELECT a.prid,a.pacid,a.shop_sn,a.prbid,a.name,a.shop_price,a.up_date,a.dateadd,a.shop_thumb,a.comms,a.hots,b.descs FROM ".$pre."pravail_product as b,".$pre."pravail_producttxt as a
		WHERE  b.prid = '".$prid."' AND a.prid = b.prid AND a.domain_id=".$Aconf['domain_id']. " LIMIT 1"); 
	}
	$rowproduct['up_date']  = ($rowproduct['up_date'])?date("Y年m月", $rowproduct['up_date']):'';
	 
	 /* 是否有历史价格查询 */
	if($row[main_prid] > 0 ) { 
		$shop_price = $oPub->getOne("SELECT shop_price FROM ".$pre."price_history where  prid = '".$prid."' AND praid ='".$praid."' AND domain_id = '".$Aconf['domain_id']."'"); 
		$rowproduct['shop_price'] = ($shop_price)?$shop_price:$rowproduct['shop_price'];
	} 
	/* 产品属性 start */ 
	 $rowp = $oPub->select("SELECT paid,pacid,attr_name  FROM ".$pre."prattri WHERE `pacid` = $rowproduct[pacid]   ORDER BY sort_order,paid ASC"); 
	 $db_table = ($row[main_prid] > 0 )? $pre."prattrival": $pre."pravail_prattrival";
	 $tmpprid  = ($row[main_prid] > 0 )? $row[main_prid]: $row[prid]; 
	 while( @list( $k, $v) = @each( $rowp) ) {
		/* 取对应值 */ 
		$rowp[$k][pavals] = $oPub->getOne("SELECT pavals  FROM ".$db_table." where `paid` = $v[paid] AND prid  =  $tmpprid limit 1"); 
	 }
	$rowproduct[prattri] = $rowp;unset($rowp); 
	/* 像册列表 */
	$db_table = ($row[main_prid] > 0 )? $pre."product_file": $pre."pravail_product_file";
	$tmpprid  = ($row[main_prid] > 0 )? $row[main_prid]: $row[prid];
	$rowproduct[img_list] = $oPub->select("SELECT thumb_url,filename,shop_thumb,descs FROM " . $db_table . " WHERE prid = '".$tmpprid."' limit 4");  
	if($rowproduct[img_list])
	{  
		$rowproduct[show_img_list] = true;
	} 


	/* 产品页显示的模块 */
	$Ahome["nowNave"]  = '<li><a href="'.$rowpra['shop_url'].'">'.$rowpra[pra_name].'</a></li><li><A HREF="'.$rowpra['shop_h'].'">热卖商品</A></li>'; 
	$Aconf["header_title"] = $rowproduct[name].'热卖商品|'.$rowpra[pra_name].'|'.$Aconf['header_title'];  
	$rowpra['product'] = $rowproduct; unset($rowproduct);
 
	$Ahome["pravail"] = $rowpra; unset($rowpra);

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 

	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?>
 