<?php
if (!defined('IN_OUN')) 
{
    die('Hacking attempt');
}  
/* 
  文件名对应表用于权限管理 
  注：需要同时修改 $Aprive $admin_nave 两数组，及nave.php文件的20行
*/
$Aprive = array(
	'list_br_0'       => '系统管理', 
	'sysconfig'       => '网站基本资料设置', 
	'other_notice'    => '网站公告',
	'other_about'     => 'about us',
	'acount_log'      => '操作日志',
	'articlelist_total_users'  => '已发新闻统计',

	'list_br_1'       => '新闻管理', 
	'articlesend'     => '新闻添加',
	'articlelist'     => '新闻列表',
	'articleverify'   => '新闻审核',
	'article_comms'   => '新闻评论',
	'pic'             => '图库【相册】',
	'articlecat'      => '新闻分类',
	'arti_attr'       => '新闻属性',


	'list_br_2'       => '产品管理',
	'productsend'     => '商品添加',
	'productlist'     => '商品列表',
	'productcat'      => '商品分类',
	'product_comms'   => '商品评论',
	'product_attrib'  => '商品属性分类',
	'product_attriblist'  => '商品属性列表',
	'product_brand'   => '商品品牌编辑', 

	'list_br_3'         => '经销商专区',
	'prav_productlist'  => '商品信息列表',
	'prav_productsend'  => '商品信息添加',
	'prav_articlelist'  => '促销信息列表',
	'prav_articlesend'  => '促销信息添加',
	'pravail_price'     => '总站产品报价',
	'prav_productcat'   => '经销商产品分类',
	'prav_other_notice' => '经销商公告',
	'prav_other_about'  => '经销商简介',
	'prav_sysconfig'    => '基本资料编辑',

	'list_br_4'       => '栏目管理', 
	'template_set'    => '网站样式选择', 
	'navigator'       => '自定义导航栏目',  
	'template_item'   => '页面显示设置',
	'template_edit'   => '代码方式修改页面',  

	'list_br_5'       => '多管理员权限', 
	'adminuser'       => '管理员权限设置',  
	'adminmy_base'	  => '修改登录密码',
	'pravailability'  => '经销商管理帐号',

	'list_br_6'       => '广告管理',
	'ad_send'         => '广告添加',
	'ad_list'         => '广告列表', 
	'tj'			  => '推荐添加',
	'tjcat'           => '推荐分类设置', 

	'list_br_7'       => '网上调查',
	'vote_title'      => '调查项添加',
	'vote_item'       =>'选项添加',
	'vote_group'      =>'可选组编辑',
	'vote_end'        =>'投票结果',

	'list_br_8'       => '常规管理',
	'keytj'          => '关键词推荐',
	'links'           => '友情链接',
	'support'         => '留言管理',
	'support_re'         => '留言回复管理', 
	'other_sernet'     => '服务网络', 

	'list_br_9'        => '超级用户管理',
	'messages'         => '站内短信',
	'sysnotice'        => '站长公告',
	'inducat'          => '行业分类列表',
	'citycat'          => '地区城市列表',
	'sysconfig_edit'   => '已申请网站管理',
	'syskey'           => '主站推荐关键词',
	'filter'           => '关键词及IP过滤', 

	'sys_ad_position_send'    => '广告位置添加',
	'sys_ad_position'         => '广告位置编辑',
	'sys_ad_list'             => '所有广告列表',  
 
	'list_br_10'        => '注册用户管理',	
	'userslist'         => '注册用户列表',
	'userstype'         => '用户类型',
	'usersjob'          => '求职列表',
	
	'list_br_11'        => '汇款或订单记录',	
	'userscw'           => '汇款充值记录',
	'usersdds'          => '订单状态',  
);
/* 导航条显示数组 */
$admin_nave["list_br_0"] = array('sysconfig','other_notice','other_about','acount_log','articlelist_total_users');  
$admin_nave["list_br_4"] = array('template_set','navigator','template_item','template_edit');
$admin_nave["list_br_5"] = array('adminuser','pravailability');
$admin_nave["list_br_6"] = array('ad_send','ad_list','tj','tjcat');
$admin_nave["list_br_1"] = array('articlesend','articlelist','articleverify','article_comms','pic','articlecat' ,'arti_attr'); 
$admin_nave["list_br_2"] = array('productsend','productlist','product_comms','productcat','product_attrib','product_attriblist','product_brand'); 
$admin_nave["list_br_3"] = array('prav_productlist','prav_productsend','prav_articlelist','prav_articlesend','pravail_price','prav_productcat','prav_other_notice','prav_other_about','prav_sysconfig');  $admin_nave["list_br_7"] = array('vote_title','vote_end'); 
$admin_nave["list_br_8"] = array('keytj','links' ,'support','support_re','other_sernet');   
$admin_nave["list_br_10"] = array('userslist','userstype','usersjob');
$admin_nave["list_br_11"] = array('userscw','usersdds'); 
if($Aconf['allow_home'] == $Aconf['domain_id'])
{
	$admin_nave["list_br_9"] = array('messages','sysnotice','inducat','citycat','sysconfig_edit','sys_ad_position_send','sys_ad_position','sys_ad_list','syskey','filter');   
} else 
{
	unset($admin_nave["list_br_9"]); 
}
/* 当前访问的文件名字 */
$str       = $_SERVER["SCRIPT_NAME"]; $Atmp = explode('/',$str );
$count     = count($Atmp); $count = $count -1; $nowfile = $Atmp[$count];
$nowfile   = str_replace('.php','',$nowfile);
if($nowfile <> 'index' && $_SESSION['auser_id'] < 1)
{
	$Aconf['priveMessage'] = '<A HREF="index.php" target="_top">登录后才能操作！</a><br/>'.$Aconf['footer_title'];
}
 /* 判断用户权限 */
if ( $_SESSION['aaction_list'] != 'all') 
{ 
	$_SESSION['aaction_list'] .=',nave'; 
	$Aaction_list = explode(',',$_SESSION['aaction_list']); 
	if(!in_array($nowfile,$Aaction_list)) 
	{
        $Aconf['priveMessage'] = '<A HREF="index.php" target="_top">你无此权限，请与管理员联系。</a><br/>'.$Aconf['footer_title'];
	}
} 
if(!isset($Aprive[$nowfile])) $Aprive[$nowfile]=false; 
$nowName = '<A HREF="../index.php">'.$Aconf['web_title'].'首页</A>><A HREF="index.php">后台管理</A>>'.$Aprive[$nowfile]; 

if($_SESSION['auser_id'] > 0) { 
	//查阅短信提醒
	$haveMessages = false; 
	$count = $oPub->getOne("SELECT count(*) as count  FROM ".$pre."messages where touser_id='".$_SESSION['auser_id']."' and type<1 and states<1");//个人短信 
	if($count < 1)
	{ //1=>'系统消息',2=>'群发消息'
		$count = $oPub->getOne("SELECT count(*) as count  FROM ".$pre."messages where type >= 1"); 
		if($count >= 1) 
		{
			$countre = $oPub->getOne("SELECT count(*) as count  FROM ".$pre."messagesread  where user_id = '".$_SESSION['auser_id']."'");  
			if($countre < $count )
			{ 
				$haveMessages = true;
			} 
		}
	}else
	{
		$haveMessages = true;
	} 
	$straud ='';
	if($haveMessages){
		$straud = '有新的未读短信';
	}  

	$str = $haveMessages?'<span style="color:#FF0000;">'.$straud.'</span>':'站内短信'; 
	$str = ' <a href="messagesuser.php" target="main">'.$str.'</a> ';  
	$str .= '<A HREF="../"   target="_brank">home page浏览首页</A> <A HREF="adminmy_base.php">change password密码修改</A> <A HREF="clear_all_files.php">refresh cached缓存刷新</A> <A HREF="logoff.php">logout退出</A>' ;
}else
{
	$str = '';
}
$Ahome["naveRight"] = $str; 
if(!isset($action)) $action=false;  
//后台管理模版目录
$smarty->template_dir   .= $Aconf['manage_dir']; 
$Aconf["template_path"] = "../".$Aconf["template_path"].$Aconf['manage_dir'];  
?>  