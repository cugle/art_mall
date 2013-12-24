<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");  
include_once( ROOT_PATH."ads.php"); 
  
 
if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}
/* 缓存编号 */
$str_cache_id = $Aconf['domain_id']; 


if ($_SESSION['user_id'] > 0) {  
		$where = ' AND domain_id = "'.$Aconf['domain_id'].'"';
  		$row = $oPub->select('SELECT *  FROM '.$pre.'carts WHERE users_id  = "'.$_SESSION['user_id'].'"'.$where.' order by dateadd asc'); 
		while( @list( $k, $v ) = @each( $row ) ) {  
			$x = $oPub->getRow('SELECT prid,name,shop_sn,shop_number,shop_price,shop_thumb FROM '.$pre.'producttxt WHERE prid = '.$v['prid']);
			$row[$k]['dateadd'] = date("Y-m-d h:i",$v['dateadd']);
			$row[$k]['shop_number']  = $x['shop_number'];
			$row[$k]['shop_price']  = $x['shop_price'];
			$row[$k]['shop_thumb']  = $x['shop_thumb'];
			$row[$k]['name']        = $x['name']; 
			$row[$k]['shop_sn']        = $x['shop_sn'];
			if($Aconf['rewrite']){
				$row[$k]["product_url"] = "product-".$x["prid"].".html";   
			}else{ 
				$row[$k]["product_url"] = "product.php?id=".$x["prid"];  
			} 
		}
		$Ahome['car'] = $row;

}
$cache_id = sprintf('%X', crc32($str_cache_id));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {  
	include_once( ROOT_PATH."includes/item_set.php");  
	assign_template($Aconf); 
	$smarty->assign('home', $Ahome ); 
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome);
} 
$smarty->display($Aconf["displayFile"], $cache_id);  
?>
