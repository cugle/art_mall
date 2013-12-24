<?php
define('IN_OUN', true);
include_once( "./includes/command.php");

include_once( ROOT_PATH."ads.php");

/* 留言提交 */
$db_table = $pre."support";
$strMessage = ''; 
//var_dump($_SESSION['vCode']);
if($_POST["act"] == 'install'){
	if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode)){ 
		$strMessage  .='验证码错误！';		
	} 
	$strMessage = ($descs == '')?' 留言内容不能为空 ':''; 
	$ip = real_ip();
	$strMessage .= ($ip == '')?'\n ERROR 1,系统错误，不能添加评论':'';
    /* 读取过滤的关键词 start */
	if(empty($strMessage)) { 
		$descs = filter($descs); 
		if(empty($descs)){
			$strMessage = '禁止的IP或关键词';
		} 
	} 

	if($Aconf['reg_support']>0 && $_SESSION['user_id']< 1)
	{
		$strMessage = '必须登录后才能留言！';
	}
    
    /* 读取过滤的关键词 end */  
    if(empty($strMessage)) {  
		
	  $nowdatedd = gmtime();
      $sql = "SELECT dateadd  FROM ".$pre."support WHERE ip='".$ip."' order by dateadd desc limit 1";
      $row = $oPub->getRow($sql);
	  $dateadd = ($row)?$nowdatedd - $row[dateadd]:31;
		if($dateadd > 30) {
			$oPub->query('INSERT INTO ' . $pre.'support( users_id,supports,ip,name,tel,pos,addrs,email,dateadd,states,domain_id) 
		VALUES ("'.$_SESSION['user_id'].'","'.$descs.'", "'.$ip.'","'.$name.'","'.$tel.'","'.$pos.'","'.$addrs.'","'.$email.'","'.$nowdatedd.'",0,"'.$Aconf['domain_id'].'")');  
		} else {
			$strMessage = '30秒后才能继续添加!';
		}
	}

	if(!empty($strMessage)){ 
		$pnid = $pnid?$pnid:0;
		if($Aconf['rewrite']){ 
			$strtmp  =  'contactus.html';  
		}else{
			$strtmp  =  'contactus.php'; 
		} 
		echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='".$strtmp."';</script>";
		exit;
	}  
	unset($_POST); 
}

//$Ahome['strMessage'] = $strMessage;

if ((DEBUG_MODE & 2) != 2){
    $smarty->caching = true;
}
/* 调用模板 */ 
$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$page));
$cache_id = ''; 
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) { 
	include_once( ROOT_PATH."includes/item_set.php");
	
	/* 产品页显示的模块 */   
	$Aconf['header_title'] = $Aweb_url['support'][0]."|".$Aconf["web_title"]; 
	/* 留言列表 */
	$strWhere = ($Aconf['support'])?" states = 3 ":" states <> 1 ";
	$strWhere = ' WHERE '.$strWhere.' AND domain_id='.$Aconf['domain_id'];
	$row = $oPub->getRow("SELECT count( * ) AS count FROM ".$pre."support".$strWhere); 
	$count = $row['count']; 
	$page = new ShowPage; 
	$page->PageSize = $Aconf['set_pagenum'];
	$page->PHP_SELF = PHP_SELF;
	$page->Total = $count;
	$pagenew = $page->PageNum();
	$page->LinkAry = array(); 
	$strOffSet = $page->OffSet();
	/* 翻页 */
	$Ahome["showpage"] = ($count > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 

	$sql = "SELECT spid,users_id,comms,ip,supports,dateadd  FROM ".$pre."support".$strWhere." ORDER BY spid desc limit ".$strOffSet; 
	$AsppAll = $oPub->select($sql);
	$n = 0 ; $count++;
	while( @list( $k, $v ) = @each( $AsppAll) ) { 
		$AsppAll[$k][dateadd] = date("y年n月j日h:i", $v['dateadd']);
		$Aip = explode(".",$v[ip]);
		$n ++ ;
		$j = $count - ($pagenew-1) * $Aconf['set_pagenum']-$n;
		$AsppAll[$k][ip] = '第'.$j.'楼 IP:'.$Aip[0].'.'.$Aip[1].'.*';
		$AsppAll[$k][nick] = empty($v['name'])?'匿名':$v['name'];
		$AsppAll[$k][descs] =  $v['supports'];

		$row = $oPub->getRow('SELECT user_name,avatar from '.$pre.'users where   id="'.$v['users_id'].'"'); 
		$AsppAll[$k]['user_name'] = (empty($row['user_name']))?'匿名':$row['user_name']; 
		if($row['avatar'] > 0)
		{ 
			$avatar    = '<IMG SRC="data/userimg/avatar_big/'.$row['avatar'].'_big.jpg" WIDTH="165" HEIGHT="200" BORDER="0" >'; 
		}else
		{//no_shoplogo
			$avatar    = '<IMG SRC="images/command/osunit_165_200.jpg" WIDTH="165" HEIGHT="200" BORDER="0" >';
		}
		$AsppAll[$k]['avatar'] = $avatar;

		
	}
	$Ahome["support"] =  $AsppAll;
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A>'.$Aconf['nav_symbol'].'</li><li>'.$Aweb_url['support'][0].'</li>'; 

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );  
	//$smarty->assign('ckeditor', create_html_pre("descs") );
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?>
