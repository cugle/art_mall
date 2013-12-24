<?php
define('IN_OUN', true);
include_once( "./includes/command.php");  
include_once( ROOT_PATH."ads.php");

//右侧 时尚调用 end     
if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}
 
$str_cache_id = $Aconf['domain_id'].$page;  
$cache_id = sprintf('%X', crc32($str_cache_id));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) { 
	
	include_once( ROOT_PATH."includes/item_set.php");
	$Aconf['header_title'] = $Aweb_url['vip'][0]."|".$Aconf["web_title"]; 
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A>'.$Aconf['nav_symbol'].'</li><li>'.$Aweb_url['vip'][0].'</li>'; 

	/* 得到所有得vip商铺图标 */
	$db_table = $pre.'sysconfig';
	$where = ' states <> 1';
	$count =$oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'sysconfig WHERE 1 AND '. $where);
	 
	$page = new ShowPage; 
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count;
	$page->PHP_SELF = PHP_SELF;
	$pagenew = $page->PageNum();
	$page->LinkAry = array(); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$Ahome["showpage"] = ($count  > $Aconf['set_pagenum'])?$page->ShowLink_num():'';  
	$Ashop_vip = $oPub->select('SELECT user_id,main_domin,header_title,shop_logo  FROM '.$pre.'sysconfig  where '.$where.'  ORDER BY scid DESC  LIMIT '. $strOffSet); 

	$StrsysAll = '';
	$n = 0;
	if($Ashop_vip){
		foreach ($Ashop_vip AS $k => $v) {
			$row = $oPub->getRow('SELECT FROM_UNIXTIME(add_time) as add_time,last_ip FROM '.$pre.'admin_user WHERE user_id='.$v['user_id']);
			$Ashop_vip[$k]['add_time'] = $row['add_time'];
			$Ashop_vip[$k]['last_ip'] = $row['last_ip'];
			if($Ashop_vip[$k]['shop_logo'] == '') {
				$rand_array=range(1,50); 
				$Ashop_vip[$k]['shop_logo'] = 'images/viplogo/osunit_'.$rand_array[$k].'.jpg';
			}
			$Ashop_vip[$k]['header_titlesub'] = sub_str($Ashop_vip[$k]['header_title'],8,false); 
		}
		$Ahome["vip"] = $Ashop_vip;
	}

    assign_template($Aconf); 
    $smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
}

$smarty->display($Aconf["displayFile"], $cache_id);  


?>
