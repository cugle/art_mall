<?php
define('IN_OUN', true);
include_once( "./includes/command.php");
include_once( ROOT_PATH."includes/item_set.php");
/* 用户注册 */
$Aconf['header_title'] = $Aweb_url['webreg'][0]."|".$Aconf["web_title"];
$Ahome["nowNave"]  = '<li><A HREF="./">首页</A> '.$Aconf['nav_symbol'].'</li><li>'.$Aweb_url['webreg'][0].'</li>'; 

$user_name = !empty($user_name)?clean_html($user_name):''; 

$Ahome['user_name'] =  !isset($user_name)?false:$user_name; 

if($action == "reg")
{

	if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode)){ 
		 echo "<SCRIPT language='javascript'>\nalert('验证码错误!!');top.location='webreg.php';</script>";
		 exit;		
	} 

	$user_name = strtolower($user_name); 
	if (preg_match('/\'\/^\\s*$|^c:\\\\con\\\\con$|[%,\\*\\"\\s\\t\\<\\>\\&\'\\\\]/', $user_name))
	{
		$strMessage = '注册失败：用户名错误！';
	} elseif($user_name == '' && $password == '')
	{ 
		$strMessage = '注册失败：用户名或者密码不能为空！';
	} elseif($password != $re_password)
	{
		$strMessage = '注册失败：两次输入的密码不同！';
	} elseif(str_len($user_name) <4 || str_len($user_name) > 26)
	{
		$strMessage = '注册失败：用户名应该为，4-26个英文字母或数字';
	} else
	{
		$strMessage =  '';
	} 

	$db_table = $pre.'admin_user';
	$user_name =  trim($user_name); 
	$Auser = $oPub->getRow("SELECT  id as user_id,user_name FROM ".$pre."users WHERE `user_name` LIKE '".$user_name."' limit 1"); 
	if($Auser['user_id'] > 0)
	{
		$strMessage = '注册失败：此用户名已经被使用！';
	}
	$strMessage =  ($strMessage ==  '')?'' :'<A HREF="'.PHP_SELF.'">'.$strMessage.'</a>';

	if($strMessage == '')
	{

		$Afields=array('user_name'=>$user_name, 'password'=>mkmd5($password),'email'=>$email,'reg_time'=>gmtime(),'ifmanger'=>1);
		$oPub->install($pre.'users',$Afields);
		$user_id =  $oPub->insert_id();

		$Afields=array('user_id'=>$user_id,'user_name'=>$user_name,'add_time'=>gmtime(), 'action_list'=>'all' );
		$oPub->install($pre."admin_user",$Afields); 

		$main_dominMessage = '';
		if($user_id)  {
			//注册配置文件
			 $db_table = $pre."sysconfig";	
			 if($_POST['main_domin']) {
				$main_domin = str_replace('http://','', $main_domin);
				$main_domin = str_replace('https://','',$main_domin);
				if(strpos($_POST['main_domin'],$Aconf['mail_url'] ) ) {
					$main_domin = $user_name.'.'.$Aconf['mail_url'];
				} else {
					$Atmp = explode(".",$main_domin);
					if(count($Atmp) < 2){
						$main_domin = $user_name.'.'.$Aconf['mail_url'];
					}
				}

				/*查询 $main_domin 是否被使用 */
				$Asc = $oPub->getRow("SELECT scid FROM ".$pre."sysconfig WHERE `main_domin` LIKE '".$main_domin."' limit 1"); 
				if($Asc['scid'] > 0)
				{
					$main_dominMessage = $main_domin.' 已经被使用,你可以通过 '.$user_name.'.'.$Aconf['mail_url'] .'访问你的网站';
					$main_domin = $user_name.'.'.$Aconf['mail_url'];
				}
			 }
			 else
			 {
				$main_domin = $user_name.'.'.$Aconf['mail_url'];
			 }

			 $notices = '欢迎光临'.$user_name.'的网站！';
			 $descs   = $user_name.' 关于本站的修改，请在：后台->常规管理->关于本站 修改！';

			 $sets='cache_time[|]0{|}logo_w[|]200{|}logo_h[|]60{|}big_thumb_w[|]240{|}big_thumb_h[|]180{|}min_thumb_w[|]90{|}min_thumb_h[|]60{|}mis_thumb_w[|]60{|}mis_thumb_h[|]54{|}nav_w[|]60{|}nav_h[|]50{|}rewrite[|]0{|}support[|]1{|}links[|]0{|}footer_title[|]版权所有 '.$user_name.'{|}icp[|]ICP备07500***号 {|}title[|]{|}keywords[|]重庆,行业之星,PHP开源全站系统,全站CMS{|}description[|]欢迎光临！{|}shop_name[|]'.$user_name.'{|}contact[|]'.$user_name.'{|}phone[|]028-6868**8{|}fax[|]{|}tel[|]1339985***9{|}zip[|]{|}address[|]*********中富大厦{|}email[|]xy**@qq.com{|}msn[|]{|}qq[|]3422***6{|}'; 

			 $home='one|notices:1,,,公告;descs:1,,,公司简介;articat:1,,,新闻分类;productcat:1,,,商品分类;vote:1,,,推荐调查;qq:0,,,在线QQ;sesspro:1,,,最近访问商品;]two|keytj:1,8,,推荐关键词;vip:0,8,,VIP客户;articles:1,5,,新闻列表;articles_top:1,3,,置顶新闻;articles_focus:1,7,,焦点新闻;articles_trundle:0,10,,滚动新闻;articles_ifpic:0,20,,新闻图库;articles_comms:0,20,,评论最多的文章;products:1,25,,商品列表;products_top:1,5,,特价促销;products_special:1,5,,畅销商品;probrand:0,20,,推荐品牌;pravail:0,20,,推荐经销商;votes:1,6,,调查列表;links:1,10,,友情连接列表;users:1,10,,新注册用户;]three|acids1:1,8,1,日志公告;acids2:1,5,20,成功案例;acids3:1,3,2,建站帮助;';

			 $sub_themes = !empty($Aconf['admin_sub_themes'])?$Aconf['admin_sub_themes']:'gz500';
			 $Afields=array('user_id'=>$user_id,'user_name'=>$user_name,'main_domin'=>$main_domin,'header_title'=>$user_name,'sets'=>$sets,'home'=>$home,'notices'=>$notices,'descs'=>$descs,'template'=>$sub_themes,'pre_scid'=>$Aconf['domain_id']);

			 $oPub->install($db_table,$Afields);
			 $scid =  $oPub->insert_id();

			 if($scid ) {
				 $oPub->query('INSERT INTO ' . $pre . 'sysconfigfast  (scid,user_name,main_domin) VALUES ("'.$scid.'","'.$user_name.'","'.$main_domin.'")');  
				 $Afields=array('domain_id'=>$scid);
				 $condition = 'user_id='.$user_id;
				 $oPub->update($pre.'admin_user',$Afields,$condition);

				 $Afields=array('domain_id'=>$scid);
				 $condition = 'id='.$user_id;
				 $oPub->update($pre.'users',$Afields,$condition); 

				 //创建缓存目录
				 /* 如果是安全模式，检查目录是否存在 */ 
				if (!is_dir($base_chachereg_dir.$scid)) {
					if (@!mkdir($base_chachereg_dir.$scid, 0777)) {
						$strMessage = '序号为：'.$scid.'的网站目录创建失败.';
					}
				} 
				if (!is_dir($base_compilereg_dir.$scid)) {
					if (@!mkdir($base_compilereg_dir.$scid, 0777)) {
						$strMessage = '序号为：'.$scid.'的网站目录创建失败。';
					}
				}
			 }

			$_SESSION['auser_id'] = $user_id;
			if(empty($strMessage)) {
				/*  增加用户默认导航数据 */ 
				$oPub->query('INSERT INTO '. $pre.'nav(  `name` , `url` , `domain_id` )VALUES ("首页","articles.php","'.$scid.'"), ("内部新闻","articles.php","'.$scid.'"), ("商品列表","products.php","'.$scid.'"), ("关于本站","about.php","'.$scid.'"), ("访客留言","support.php","'.$scid.'"), ("服务网络","sernet.php","'.$scid.'")'); 
				/* 添加默认分类 FLASH轮播广告	443	235	5	1	268 */
				$oPub->query('INSERT INTO '. $pre.'tjcat(name,imgwidth,imgheight,limits,showtype,orders,domain_id)VALUES("FLASH轮播广告",1005,240,5,1,1,"'.$scid.'"),("企业荣誉",228,171,5,1,2,"'.$scid.'")'); 
				/* 添加默认分类 articat 及·首页新闻标签*/  
				$oPub->query('INSERT INTO '. $pre.'articat(name,ifshow,domain_id)VALUES("本站公告",1,"'.$scid.'"),("行业新闻",1,"'.$scid.'"),("公司资讯",1,"'.$scid.'"),("重点项目",1,"'.$scid.'")');
				/* 增加首页标签 */
				//three|acids1:1,8,25,本站公告;
				$acid = $oPub->getOne('SELECT acid FROM '. $pre.'articat WHERE domain_id="'.$scid.'" limit 1'); 
				$str = 'three|acids1:1,8,'.$acid.',本站公告;acids2:1,8,'.($acid+1).',行业新闻;acids3:1,8,'.($acid+2).',公司资讯;acids4:1,8,'.($acid+3).',重点项目;';

				$home='one|notices:1,,,公告;descs:1,,,公司简介;articat:1,,,新闻分类;productcat:1,,,商品分类;vote:1,,,推荐调查;qq:0,,,在线QQ;sesspro:1,,,最近访问商品;]two|keytj:1,8,,推荐关键词;vip:1,8,,VIP客户;articles:1,5,,新闻列表;articles_top:1,3,,置顶新闻;articles_focus:1,7,,焦点新闻;articles_trundle:0,10,,滚动新闻;articles_ifpic:0,20,,新闻图库;articles_comms:0,20,,评论最多的文章;products:1,25,,商品列表;products_top:1,5,,特价促销;products_special:1,5,,畅销商品;probrand:0,20,,推荐品牌;pravail:0,20,,推荐经销商;votes:1,6,,调查列表;links:1,10,,友情连接列表;users:1,10,,新注册用户;]'.$str;
				$Afields=array('home'=>$home);
				$condition = 'scid='.$scid;
				$oPub->update($pre.'sysconfig',$Afields,$condition); 
				$oPub->query('INSERT INTO '. $pre.'arti_attr(attr_name,domain_id)VALUES("默认属性","'.$scid.'")'); 

				$strMessage = '注册成功：<br/><br/>';
				$strMessage .= '1.<A HREF="http://'.$user_name.'.'.$Aconf['mail_url'].'/'.$SUBPATH.'admin">填写网站基本资料>></A><br/>';
				if($main_dominMessage != '')
				{
					$strMessage .=  '2.'.$main_dominMessage;
				}else
				{
					$strMessage .= '2.<A HREF="http://'.$user_name.'.'.$Aconf['mail_url'].'/'.$SUBPATH.'">浏览默认网站样式：</A>';
				}

			}
		} else {
		 $strMessage = '<A HREF="http://'.$PHP_SELF.'">注册失败：未知错误，请与管理员联系！>></a>';
		}
	} else {
		$reg_succ = false;
	}
} 
$Ahome['strMessage'] = $strMessage;
 
$smarty->is_cached($Aconf["displayFile"], $cache_id);
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
unset($Ahome);  
$smarty->display($Aconf["displayFile"], $cache_id); 
?>
