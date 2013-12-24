<?php
define('IN_OUN', true);
include_once( "./includes/command.php");

include_once( ROOT_PATH."ads.php");
/* 产品页显示的模块 */   
$Aconf['header_title'] = $Aweb_url['jobadd'][0]."|".$Aconf["web_title"]; 
$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li>'.$Aweb_url['jobadd'][0].'</li>'; 

//添加 求职申请
$Ahome['strMessages'] = '';
if($act == 'install' && !empty($xingming) && !empty($idc) && !empty($yingpingzw))
{
	// xueli  qizhitime2 biyexx zhuangye waiyucz
	
	$bieyexx = '';
	if($xueli){
		foreach ($xueli AS $k => $v) {
			$bieyexx .= 'xueli['.$k.'][|]'.$v.'{|}';
			$bieyexx .= 'qizhitime2['.$k.'][|]'.$qizhitime2[$k].'{|}';
			$bieyexx .= 'biyexx['.$k.'][|]'.$biyexx[$k].'{|}';
			$bieyexx .= 'zhuangye['.$k.'][|]'.$zhuangye[$k].'{|}';
			$bieyexx .= 'waiyucz['.$k.'][|]'.$waiyucz[$k].'{|}'; 
			$bieyexx .= ';';
		}
	}
	// qizhitime3 danwei zhiwu lizhiyn hengminr lianxifs
	$gongzuojl = '';
	if($qizhitime3){
		foreach ($qizhitime3 AS $k => $v) {
			$gongzuojl .= 'qizhitime3['.$k.'][|]'.$v.'{|}';
			$gongzuojl .= 'danwei['.$k.'][|]'.$danwei[$k].'{|}';
			$gongzuojl .= 'zhiwu['.$k.'][|]'.$zhiwu[$k].'{|}';
			$gongzuojl .= 'lizhiyn['.$k.'][|]'.$lizhiyn[$k].'{|}';
			$gongzuojl .= 'hengminr['.$k.'][|]'.$hengminr[$k].'{|}'; 
			$gongzuojl .= 'lianxifs['.$k.'][|]'.$lianxifs[$k].'{|}'; 
			$gongzuojl .= ';';
		}
	}
 
	$Afields=array('users_id'=>$_SESSION['user_id'],'xingming'=>$xingming,'sex'=>$sex,'mingzu'=>$mingzu,'hunyingzk'=>$hunyingzk,'shengri'=>$shengri,'email'=>$email,'tel'=>$tel,'idc'=>$idc,'jingjitel'=>$jingjitel,'addres'=>$addres,'yingpingzw'=>$yingpingzw,'arid'=>$id,'jobstate'=>$jobstate,'qiwangxz'=>$qiwangxz,'daogangtime'=>$daogangtime,'bieyexx'=>$bieyexx,'gongzuojl'=>$gongzuojl,'descs'=>$descs,'dateadd'=>gmtime(),'addip'=>real_ip(),'domain_id'=>$Aconf['domain_id']);
	$oPub->install($pre.'users_job',$Afields); 
	$Ahome['strMessages'] = '职位申请表已提交。'; 
	$id = '';
	 echo "<SCRIPT language='javascript'>\nalert('".$Ahome['strMessages']."');top.location='articles.php';</script>";
	 exit;
}

if($id > 0)
{
	$Ahome['yingpingzw']= $oPub->getOne('SELECT name FROM '.$pre.'artitxt WHERE  arid="'.$id.'"  limit 1'); 
}

$row = $oPub->getRow('SELECT * FROM '.$pre.'users WHERE  id="'.$_SESSION['user_id'].'"  limit 1');   
if($row['avatar'] > 0){
	$user_avatar = 'data/userimg/avatar_big/';
	if(file_exists($user_avatar .$row['avatar'].'_big.jpg' )){
		$row['avatar'] = $user_avatar.$row['avatar'].'_big.jpg';
	}	
}
$Ahome['user'] = $row; unset($row);
if ((DEBUG_MODE & 2) != 2){
    $smarty->caching = true;
}
/* 调用模板 */  
$cache_id = sprintf('%X', crc32($Aconf['domain_id']));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {
	include_once( ROOT_PATH."includes/item_set.php"); 
	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );  
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id); 
?> 