<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");

$Aid = explode("a",$id);
$praid = $id = $Aid[0] + 0;//商户ID  

 /* 评论提交 */ 
$strMessage = ''; 
if($_POST[act] == 'install') {
	if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode)){ 
		$strMessage  ='验证码错误！';		
	} 
	$descs = clean_html($_POST["descs"]);
	$strMessage .= ($descs == '')?' 评论内容不能为空':'';
	$ip = real_ip();
	$strMessage .= ($ip == '')?'\n ERROR 1,系统错误，不能添加评论':'';
    /* 读取过滤的关键词 start */
	if(empty($strMessage)) { 
		$descs = filter($descs); 
		if(empty($descs)){
			$strMessage = '禁止的IP';
		}
	} 

	if($Aconf['reg_support']>0 && $_SESSION['user_id']< 1)
	{
		$strMessage .= '必须登录后才能留言！';
	}

    if(empty($strMessage))
	{
	  $prid = $prid + 0;
	  $nowdatedd = gmtime();
      $row = $oPub->getRow('SELECT dateadd  FROM '.$pre.'pravail_product_comms WHERE ip="'.$ip.'" order by dateadd desc limit 1'); 
	  $dateadd = ($row)?$nowdatedd - $row['dateadd']:31;
	  if($dateadd > 30) {
			$oPub->query('INSERT INTO '. $pre.'pravail_product_comms (praid,prid, descs ,  ip , email ,name,  dateadd , states , `domain_id` )VALUES ("'.$praid.'","'.$prid.'","'.$_POST['descs'].'","'.real_ip().'","'.$_POST['email'].'","'.$_POST['name'].'","'.gmtime().'",0,"'.$Aconf['domain_id'].'")');
			$strMessage =  "添加成功!";
			if($prid > 0 ) { 
			 $oPub->query('UPDATE '. $pre.'pravail_producttxt SET comms= comms+ 1 WHERE `prid` ="'.$prid.'" and `domain_id`="'.$Aconf['domain_id'].'"'); 
			}
			/* 如果是登陆用户，测记录用户文章 id coms_type = 1/2/3/4 */
			if ($_SESSION['user_id']) {
				$tmp = $oPub->getOne('SELECT users_id  FROM '.$pre.'users_comms WHERE users_id="'.$_SESSION['user_id'].'" AND coms_type = 4 AND arid = "'.$praid.'"'); 
				if ( !$tmp ) {				  
					$oPub->query('INSERT INTO '.$pre.'users_comms (users_id, coms_type ,arid,dateadd,domain_id) VALUES ("'.$_SESSION['user_id'].'",4, "'.$praid.'","'.gmtime().'","'.$Aconf['domain_id'].'")');  
				} else {
					$oPub->query('UPDATE '.$pre.'users_comms  SET dateadd = "'.gmtime().'"  WHERE users_id = "'.$tmp.'"'); 
				}
			}//$_SESSION['user_id']   
		} else {
          $strMessage = '30秒后才能继续添加!'; 
		}
	}
	unset($_POST);  
	//评论结束
	if(!empty($strMessage)){  
		if($Aconf['rewrite']){ 
			$strtmp  =  'shop_c-'.$praid.'-0.html';  
		}else{
			$strtmp  =  'shop_c.php?id='.$praid; 
		} 
		echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='".$strtmp."';</script>";
		exit;
	}  
} 

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$prid.$page));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {
	include_once( ROOT_PATH."includes/item_set.php"); 
	include_once( ROOT_PATH."includes/shopcomm.php"); 
	/* 评论列表 */
	$where_comms = ($Aconf['support'])?" states = 3 ":"  states <> 1 ";
	$strWhere = ' WHERE '.$where_comms.' and domain_id='.$Aconf['domain_id'];
	$count = $oPub->getOne('SELECT count( * ) AS count FROM '.$pre.'pravail_product_comms '.$strWhere); 
	$page = new ShowPage;
	$page->PageSize = $Aconf['set_pagenum'];
	$page->Total = $count;
	$page->PHP_SELF = PHP_SELF;
	$pagenew = $page->PageNum(); 
	$page->LinkAry = array("id"=>$praid); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$rowpra["showpage"] = ($count > $Aconf['set_pagenum'])?$page->ShowLink_num():'';  

	$AsppAll = $oPub->select('SELECT * FROM '.$pre.'pravail_product_comms '.$strWhere.' ORDER BY prcid desc limit '.$strOffSet); 
	$n = ($_REQUEST["page"] > 1)? $Aconf['set_pagenum'] * ($_REQUEST["page"] - 1):$count; 
	while( @list( $k, $v ) = @each( $AsppAll) ) {
		 
		$AsppAll[$k]["dateadd"] = date("y年n月j日h:i", $v["dateadd"]);
		$Aip = explode(".",$v[ip]);
		$AsppAll[$k]["ip"] = '第'.$n.'楼 IP:'.$Aip[0].'.'.$Aip[1].'.*';
		$AsppAll[$k]["name"] = empty($v["name"])?'匿名':$v["name"];
		$AsppAll[$k]["descs"] =  $v["descs"] ; 
		$n -- ;
	}
	$rowpra["pravail_comms"] = $AsppAll;

	$Ahome["nowNave"]  = '<li><a href="'.$rowpra['shop_url'].'">'.$rowpra['pra_name'].'</a></li><li>评论</li>';  
	$Aconf["header_title"] = '评论|'.$rowpra[pra_name].'|'.$Aconf['header_title'];  
 
	$Ahome["pravail"] = $rowpra; unset($rowpra);

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?> 