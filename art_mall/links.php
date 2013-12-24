<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");
include_once( ROOT_PATH."ads.php");  
include_once( ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image(); 

/* 友情连接提交 */ 
if( $act == 'install' ) { 

	if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode)){ 
		$strMessage  .='验证码错误！';
		$action = false;
	}  

	if($act == 'install' && !empty($site_url) && !empty($lk_name) )
	{ 
		$Anorm = $oPub->getRow('SELECT lkid FROM '.$pre.'links where (lk_name = "'.$lk_name.'" ||  site_url = "'.$site_url.'") AND domain_id='.$Aconf['domain_id']. ' LIMIT 1'); 
		if($Anorm['lkid'] > 0 ) {
			$strMessage = '此连接已存在,不能重复添加';
		} else
		{
			$is_show = 0;
			$site_url =  str_replace('http://','', $site_url);
			$site_url = "http://".$site_url;
			$_POST['sort_order'] = 9999; 

			/*处理shop_logo图片*/
			if($_FILES['lk_logo']['size'] > 0 ) {
				/* 判断图像类型 */
				if (!$image->check_img_type($_FILES['lk_logo']['type'])) {
					$strMessage =  '图片类型错误'; 
				} else { 
				   $lk_logo = basename($image->upload_image($_FILES['lk_logo'],'links'));
				}
			} else {
				$lk_logo = '';
			}

			$lk_desc = clean_html($lk_desc);
			$Afields =array('lk_name'=>$lk_name,'lk_logo'=>$lk_logo,'lk_desc'=>$lk_desc,'site_url'=>$site_url,'sort_order'=>$_POST['sort_order'],'is_show'=>$is_show,'domain_id'=>$Aconf['domain_id']); 
			$tlkid   = $oPub->install($pre."links",$Afields);
			$strMessage = '添加成功';
		}  

	}else{
		$strMessage = '请填写完整资料';
	}

	if(!empty($strMessage)){
		$url = ($Aconf['rewrite'])?'links-0.html':'links.php';
		echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='".$url."';</script>";
		exit;
	}

	unset($Anorm);unset($_POST);
}
/* 连接列表 */ 
if ((DEBUG_MODE & 2) != 2) {
    $smarty->caching = true;
}
/* 调用模板 */
 
/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$page));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {  
	include_once( ROOT_PATH."includes/item_set.php");
	//page
	$Aconf['header_title'] = $Aweb_url['links'][0]."|".$Aconf["web_title"];
	$strWhere = ($Aconf['links'])?" is_show=1 AND ":"  ";
	$strWhere = " WHERE ".$strWhere."  domain_id=".$Aconf['domain_id'];
	$count = $oPub->getOne("SELECT count( * ) AS count FROM ".$pre."links ".$strWhere);   
	$page = new ShowPage; 
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count;
	$pagenew = $page->PageNum();
	$page->PHP_SELF = PHP_SELF;
	$page->LinkAry = array(); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$Ahome["showpage"] = ($count  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 

	$Alinks = $oPub->select("SELECT lk_name,lk_logo,lk_desc,site_url  FROM ".$pre."links ".$strWhere."  ORDER BY sort_order,lkid ASC limit ".$strOffSet); 
	$n = 0;
	while( @list( $key, $value ) = @each( $Alinks) ) {
		  $Alinks[$key]['lk_logo'] = ($value['lk_logo'] == '')?false:'data/links/'.$value['lk_logo']; 
		  $Alinks[$key]['lk_desc'] = sub_str(clean_html($value['lk_desc']),60);
	}
	$Ahome["links"] = $Alinks;unset($Alinks);
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li>'.$Aweb_url['links'][0].'</li>';  


    assign_template($Aconf); 
    $smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
    unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);


?>
