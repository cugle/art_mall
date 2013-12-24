<?php
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
$Aop = array('r'=>'User Register用户注册','f'=>'找回密码','l'=>'Login登录','e'=>'Edit profile 编辑个人资料','p'=>'Edit password 修改登录密码','d'=>'Your status 个人状态','i'=>'修改头像','c'=>'我参与的评论','emailcheck'=>'邮箱验证','findpass'=>'重新设置密码','addrs'=>'收货地址','car'=>'购物车','sc'=>'收藏管理','ding'=>'订单管理','new'=>'最新产品','cx'=>'促销商品','special'=>'畅销推荐','qh'=>'缺货列表','cvs'=>'CVS批量下单','keep'=>'收支明细','detail'=>'汇款明细');
 
 

if($_SESSION['user_id'] > 0)
{
	$o = empty($o)?'d':$o; 
}else
{
	$o = empty($o)?'l':$o; 
}
$Ahome['user_o'] = $o; 
//用户退出
if($o == 'o')
{
	$user->logout(); 
	echo "<SCRIPT language='javascript'>top.location='./';</script>";
	exit;	
}

$user_name = !empty($user_name)?clean_html($user_name):'';
$email     = !empty($email)?clean_html($email):'';
$fd        = !empty($fd)?clean_html($fd):'';
$o         = !empty($o)?clean_html($o):'';

//找回密码,发送邮件
if($o == 'f' && !empty($email))
{ 
	$email = clean_html($email);
	if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode)){ 
		 $strMessage = '验证码错误!!'; 
		 $allow = false;
	}
	//找验证邮箱
	if(is_email($email))
	{
		$row = $oPub->getRow('SELECT id,password FROM '.$pre.'users WHERE   `email` LIKE "'.$email.'" limit 1');
		$id = $row['id']; 
		if($id > 0 )
		{
			 $t = $row['password'].$id;
			 $password = mkmd5($t);
			 $strMessage = '新密码验证邮件，已经发送到邮箱中，请验证后登录!' ;
			 $strMessage .= '<iframe width="0" height="0" scrolling="no" frameborder="no" src="smtp.php?op=findpass&id='.$id.'&p='.$password.'"></iframe>'; 
			 $allow = true;
		}else
		{
			 $strMessage = '系统中，没有此邮箱!' ;
			 $allow = false;
		}
	}else
	{
		$strMessage = '邮箱输入错误!';
		$allow = false;
	}
	if(!$allow) 
	{
		 echo "<SCRIPT language='javascript'>\nalert('".$strMessage."');top.location='user.php?o=f';</script>";
		 exit;
	}
}

//通过邮件连接 重设密码
if($o == 'findpass')
{
	
	if($id > 0 && !empty($fd) && empty($up))
	{
		//http://www.osunit.cn/user.php?o=findpass&id=45&fd=4f0c7811de71dea440a41cc00ad78816
		$Ahome['fd'] = $fd;
		$Ahome['id'] = $id;
	}

	if($id > 0 && !empty($fd) && $up == 'yes')
	{
		if($password == $re_password && !empty($password))
		{
			$user_id = $oPub->getOne('SELECT user_id FROM '.$pre.'userfindpass WHERE   user_id="'.$id.'" and findpass="'.$fd.'" limit 1'); 
 			if($user_id > 0)
			{ 
				//执行修改
				$password = mkmd5($password);
				$oPub->query( 'delete from '.$pre.'userfindpass where user_id="'.$id.'"' );
				$oPub->query( 'UPDATE '. $pre.'users SET password="'.$password.'" WHERE id ="'.$id.'"'); 
				echo "<SCRIPT language='javascript'>\nalert('修改成功，重新登录!!');top.location='user.php?o=l';</script>";
				exit;
			}else
			{
				$strMessage = '修改失败，请重试！';
			}
			
		}else
		{
			$strMessage = '两次输入的密码不同！';
		}
	
	}
	$Ahome['fd'] = $fd;
	$Ahome['id'] = $id;
} 
//用户注册
if($o == 'r' && !empty($user_name) && !empty($email) && !empty($password))
{
	$user_name = getUtf8( "$user_name" );  
	 
	if(strtoupper($_SESSION['vCode']) != strtoupper($vcode) || empty($vcode)){ 
		 echo "<SCRIPT language='javascript'>\nalert('验证码错误!!');top.location='user.php?o=r';</script>";
		 exit;		
	} 
 
 
	if($user_name == '' && $password == '')
	{ 
		$strMessage = '注册失败：用户名或者密码不能为空！';
	}
	elseif($password != $re_password)
	{
		$strMessage = '注册失败：两次输入的密码不同！';
	} elseif(str_len($user_name) <2 || str_len($user_name) > 26)
	{
		$strMessage = '注册失败：用户名应该为，4-26个字符';
	}elseif(!is_email($email))
	{
		$strMessage = '注册失败：邮箱地址错误';
	} else
	{
		$strMessage =  '';
	} 
 
	$user_name = trim($user_name);
	$password_old =  trim($password);
	$password  = mkmd5($password_old);
	$Auser = $oPub->getRow('SELECT id FROM '.$pre.'users WHERE (`user_name` LIKE "'.$user_name.'" or `email` LIKE "'.$email.'") limit 1'); 
	if($Auser['id'] > 0)
	{
		$strMessage = '注册失败：此帐号或邮箱已经使用！';
	}
	$strMessage =  ($strMessage ==  '')?'' :'<A HREF="'.PHP_SELF.'">'.$strMessage.'</a>';

	if($strMessage == '')
	{ 
		$utid = $oPub->getOne('SELECT id  FROM '.$pre.'userstype where domain_id="'.$Aconf['domain_id'].'" order by  orders asc  limit 1'); 
 		$Afields=array('utid'=>$utid,'user_name'=>$user_name,'password'=>$password,'email'=>$email,'reg_time'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
		$id = $oPub->install($pre.'users',$Afields); 
 
		$strMessage = $user_name.'成功注册!'; 

        if ($user->login($user_name, $password_old))
        {
			update_user_info();  
		} 

		$userstr = '&nbsp;&nbsp;&nbsp;&nbsp; <b>'.$user_name.' 成功注册：</b><br/><br/>';
		$userstr.= '&nbsp;&nbsp;&nbsp;&nbsp; <A HREF="user.php?o=d">进入用户中心 >>></A> <br/><br/>';
		$userstr.= '&nbsp;&nbsp;&nbsp;&nbsp; <A HREF="./">返回网站首页 >>></A> ';
		$Ahome['userstr'] = $userstr;

		$o = 'd';

	} 
}
//用户登录框 
if(($o == 'l' || $op == 'l')  && !empty($user_name) &&   !empty($password))
{ 
	if($user_name == '' && $password == '')
	{ 
		$strMessage = '登录失败：用户名或者密码不能为空！';
	} elseif(str_len($user_name) <2 || str_len($user_name) > 26)
	{
		$strMessage = '登录失败：用户名应该为，4-26个字符';
	} else
	{
		$strMessage =  '';
	} 
 
	if(empty($strMessage))
	{  
        if ($user->login($user_name, $password))
        {  
			update_user_info(); 
			$strMessage = 'successful login 登录成功';  
			$userstr = '&nbsp;&nbsp;&nbsp;&nbsp; '.$user_name.' successful login 登录成功：<br/>';
			$userstr.= '&nbsp;&nbsp;&nbsp;&nbsp; <A HREF="user.php?o=d">user center 进入用户中心 >>></A> <br/>';

			$ifmanger = $oPub->getOne('SELECT ifmanger  FROM '.$pre.'users where id="'.$_SESSION['user_id'].'" and domain_id="'.$Aconf['domain_id'].'"   limit 1');
			if($ifmanger > 0 )
			{
				$userstr.= '&nbsp;&nbsp;&nbsp;&nbsp; <A HREF="'.$Aconf['manage_dir'].'">backend进入网站管理后台 >>></A> <br/>';
			}
			$userstr.= '&nbsp;&nbsp;&nbsp;&nbsp; <A HREF="./">homepage 返回网站首页 >>></A> ';
			$Ahome['userstr'] = $userstr; 
			$o = 'd'; 
		}else
		{
			$strMessage = 'Login failed 登录失败，用户名或密码错误';
		} 
	} 
}

//用户 ajax 登录
if($o == 'al' && !empty($topuser_name) && !empty($toppassword))
{
	$user_name = getUtf8( "$topuser_name" );
	$password  = getUtf8( "$toppassword" ); 
	$loginok = false;
	$strMessage =  '';
	if($user_name == '' && $password == '')
	{ 
		$strMessage = 'Login failed 登录失败';
	} elseif(str_len($user_name) <2 || str_len($user_name) > 26)
	{
		$strMessage = 'Login failed 登录失败';
	} 
 
	if(empty($strMessage))
	{ 
		$user_name = clean_html(trim($user_name));
		$password  =  trim($password);
        if ($user->login($user_name, $password))
        {  
			update_user_info();  
			$strMessage = 'Login successful 登录成功';  
			$loginok = true; 
		}else
		{
			$strMessage = 'Login failed 登录失败，用户名或密码错误';
		}  
	} 

	if(!empty($strMessage))
	{
		//假如购物车购买登录 
		if($dgnums > 0 && $prid > 0)
		{
			if($loginok)
			{
				//加入购物车  
				$row = $oPub->getRow("SELECT prid,shop_number,shop_price FROM ".$pre."producttxt WHERE prid = $prid and states <> 1 and shop_number >= $dgnums   LIMIT 1"); 
				if($row['prid'] > 0 )
				{  
					 $oPub->query('delete from '.$pre.'carts where users_id='.$_SESSION['user_id'].' and prid='.$prid);
					 $Afields=array('users_id'=>$_SESSION['user_id'],'prid'=>$prid,'nums'=>$dgnums,'sellprice'=>$row['shop_price'],'prices'=>$row['shop_price'],'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
					 $oPub->install($pre.'carts',$Afields);
					 $str = '<div style="color:#00F;font-weight: bold;margin: 5px 0 5px 0">successful....</div>';
				}else
				{
					$str = '<div style="color:#00F;font-weight: bold;margin: 5px 0 5px 0">已缺货,购买不成功...</div>';
				}
				$str .= '<A HREF="user.php?o=car">go to cart</A> <a href="javascript:;" onclick="buyhidden();">continue shopping</A>'; 
			}else
			{
				$str  = '<div style="color:#00F;font-weight: bold;margin: 5px 0 5px 0">帐号密码错误，重新登录...</div>';
				$str  .= '帐号:<input name="topuser_name" id="buypuser_name" type="text" style="width:60px"/>';
				$str  .= '密码:<input name="toppassword" id="buyppassword" type="password" value="" style="width:60px" />';
				$str  .= '<INPUT TYPE="submit" value="登录" style="height:20px;width:46px" onMousedown="ajaxLoginbuy('.$prid.')" />';
				$str  .= '<INPUT TYPE="hidden" name="o" value="l">';
			} 
		}elseif($op == 'support')
		{
			//留言板登录
			if($loginok)
			{
				$str  = '您好 '.$user_name .'请留言';
			}else
			{
				$str  = $strMessage.',帐号:<input name="topuser_name" id="buypuser_name" type="text" style="width:60px"/>';
				$str  .= '密码:<input name="toppassword" id="buyppassword" type="password" value="" style="width:60px" />';
				$str  .= '<INPUT TYPE="submit" value="登录" style="height:18px;width:36px" onMousedown="ajaxLoginbuy('.$prid.')" />';
				$str  .= '<INPUT TYPE="hidden" name="o" value="l">注册登录后才能留言，<A HREF="user.php?o=r">注册新会员</A>';
			}  
		}else
		{ 
			if($loginok)
			{
				$uR = $oPub->getRow('SELECT ifmanger,avatar  FROM '.$pre.'users where id="'.$_SESSION['user_id'].'" and domain_id="'.$Aconf['domain_id'].'"   limit 1');
				if($uR['avatar'] > 0)
				{
					$avatar = '<img src="data/userimg/avatar_small/'.$uR['avatar'].'_small.jpg" width="26" height="26" title="'.$user_name.'successful login 成功登录">';
				}else
				{
					$avatar = '<img src="images/command/user_'.$_SESSION['sex'].'.png" width="26" height="31" title="successful login 成功登录">';
				}
				$str  = '<li style="width:26px;float:left;">'.$avatar.'</li>';
				$str .= '<li style="width:110px;float:left;color:#0080C0">welcome您好 '.$user_name.' </li>';
				$str .= '<li style="width:110px;float:left;">';
				$str .= '<A HREF="user.php" style="color:#F00">user center 用户中心</A>';


				if($uR['ifmanger'] > 0 )
				{
					$str .= ' <A HREF="'.$Aconf['manage_dir'].'" style="color:#C00">backend 后台管理</A>';
				} 

				$str .= '</li>'; 
				$str .= '<li style="width:70px;float:left;"><A HREF="user.php?o=o">logout 退出登录</A></li>'; 
			}else
			{
				if(!empty($strMessage))
				{
					$str  = '<li style="width:26px;float:left;"><img src="images/command/user_error.png" width="26" height="31" title="'.$strMessage.'"></li>';
				}else
				{
					$str  = '<li style="width:28px;float:left;"><img src="images/command/user_n.png" width="26" height="31" alt="please login 请登录"></li>'; 
				} 
				
				$str  .= '<li style="width:100px;float:left;">Account 帐号:<input name="topuser_name" id="topuser_name" type="text" style="width:60px"/></li>';
				$str  .= '<li style="width:100px;float:left;">Password 密码:<input name="toppassword" id="toppassword" type="password" value="" style="width:60px"/></li>';
				$str  .= '<li style="width:44px;float:left;"><INPUT TYPE="submit" value="login 登录" style="height:20px;width:40px" onMousedown="ajaxLogin()" /></li>';
				$str  .= '<li style="width:120px;font-size: 14px;float:left;font-size: 14px"> &nbsp;<a href="user.php?o=r">Register 注册</a> | <a href="user.php?o=f">forget password 忘记密码</a></li>';  
				$str  .= '<INPUT TYPE="hidden" name="o" value="l">';
			}
		}
		echo $str;
	} 
	exit; 
	
} 
//用户 ajax 登录 end

//用户已经登录 start
if($_SESSION['user_id'] > 0)
{
	$row = $oPub->getRow('SELECT * FROM '.$pre.'users WHERE  id="'.$_SESSION['user_id'].'"  limit 1');  
	$row['reg_time']   = date("Y年m月d日 H:i",$row['reg_time']); 
	$row['last_login'] = date("Y年m月d日 H:i",$row['last_login']);
	//头像
	if($row['avatar'] > 0){
		$user_avatar = 'data/userimg/avatar_big/';  
		if(file_exists($user_avatar .$row['avatar'].'_big.jpg' )){
			$row['avatar'] = $user_avatar.$row['avatar'].'_big.jpg?x='.rand(1,10000);
		}else{
			$row['avatar'] = false;
			$oPub->query('UPDATE '.$pre.'users set avatar= 0 WHERE  id = "'.$_SESSION['user_id'].'" and domain_id="'.$Aconf['domain_id'].'"  limit 1');
		}
	}
	//邮箱验证 短信验证状态
	$Urow = $oPub->getRow('SELECT * FROM '.$pre.'usersverify WHERE  user_id="'.$_SESSION['user_id'].'"  limit 1'); 
	$row['estats'] = $Urow['estats'];
	$row['tstats'] = $Urow['tstats']; 
	//级别
	$Urow = $oPub->getRow('SELECT name,orders  FROM '.$pre.'userstype  WHERE  orders="'.$row['utid'].'"  limit 1');
	$row['userstype']      = $Urow['orders'];
	$row['userstype_name'] = $Urow['name']; 

	$Ahome['user'] = $row;
	unset($row); unset($Urow);
	//修改密码 start
	if($o == 'p')
	{
		if(!empty($old_password))
		{
			$old_password = mkmd5( $old_password ); 
			$id = $oPub->getOne('SELECT id FROM '.$pre.'users WHERE  id = "'.$_SESSION['user_id'].'" and `password` = "'.$old_password.'" and domain_id="'.$Aconf['domain_id'].'"  limit 1'); 
			if($id > 0 )
			{
				if($new_password == $renew_password && !empty($new_password))
				{
					$new_password = mkmd5($new_password);
					$oPub->query('UPDATE '.$pre.'users set password= "'.$new_password.'" WHERE  id = "'.$_SESSION['user_id'].'" and domain_id="'.$Aconf['domain_id'].'"  limit 1');
					$strMessage = '密码修改成功！';
					unset($_SESSION['user_id']);
					unset($_SESSION['user_name']);
					echo "<SCRIPT language='javascript'>\nalert('".$strMessage."');top.location='user.php?o=l';</script>";
					exit;	 
				}else
				{
					$strMessage = '新密码重复错误';
				} 
			}else
			{
				$strMessage = '旧密码错误';
			} 
		}
	}
	//修改密码 end
	//修改基本资料 start
	if($o == 'e')
	{
		//sex birthday  qq mobile_phone addrs userhttp usertag
		if(!empty($userhttp))
		{ 
			$userhttp = str_replace("http://", '',$userhttp); 
			$userhttp = str_replace("https://", '',$userhttp); 
			$userhttp = 'http://'.$userhttp; 
		} 
		if(!empty($birthday))
		{ 
			$oPub->query('UPDATE '.$pre.'users set sex= "'.$sex.'",birthday= "'.$birthday.'",qq= "'.$qq.'",mobile_phone= "'.$mobile_phone.'",addrs= "'.$addrs.'",userhttp= "'.$userhttp.'",usertag= "'.$usertag.'" WHERE  id = "'.$_SESSION['user_id'].'" and domain_id="'.$Aconf['domain_id'].'"  limit 1');
			echo "<SCRIPT language='javascript'>top.location='user.php?o=d';</script>";
			exit;
		}
	}
	//修改基本资料 end
	//修改头像 start
	if($o == 'i')
	{ 
		if($act=="upload")
		{
			@header('Content-type: text/html; charset=utf-8'); 
			@header("Expires: 0");
			@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
			@header("Pragma: no-cache");
			//define('SD_ROOT', dirname(__FILE__).'/');
			$pic_id = $_SESSION['user_id'];//使用时间来模拟图片的ID.
			$pic_path = $ROOT_PATH.'data/userimg/avatar_origin/'.$pic_id.'.jpg';
			//上传后图片的绝对地址
			//$pic_abs_path = $Aconf['domain_url'].'data/userimg/avatar_origin/'.$pic_id.'.jpg'; 
			//保存上传图片.
			if(empty($_FILES['Filedata'])) {
				echo '<script type="text/javascript">alert("对不起, 图片未上传成功, 请再试一下");</script>';
				exit();
			} 
			$file = @$_FILES['Filedata']['tmp_name']; 
			file_exists($pic_path) && @unlink($pic_path); 
			$imgType = array(1=> 'image/jpeg', 2=> 'image/pjpeg',3=> 'image/gif', 4=> 'image/png',5=>'image/x-png');
			if(!in_array($_FILES['Filedata']['type'], $imgType)) 
			{
				@unlink($_FILES['Filedata']['tmp_name']);
				echo '<script type="text/javascript">alert("上传失败,图片类型错误！");</script>'; 
			}else
			{ 
				if (!move_uploaded_file($_FILES['Filedata']['tmp_name'], $pic_path))
				{
					@unlink($_FILES['Filedata']['tmp_name']);
					echo '<script type="text/javascript">alert("上传失败，请与系统管理员联系！");</script>';
				}else
				{
					@unlink($_FILES['Filedata']['tmp_name']);
					$oPub->query('UPDATE '.$pre.'users set avatar= "'.$pic_id.'" WHERE  id = "'.$_SESSION['user_id'].'" and domain_id="'.$Aconf['domain_id'].'"  limit 1');
				} 
			}
			//写新上传照片的ID.
			$avatar = $oPub->getOne('SELECT avatar FROM '.$pre.'users WHERE  id = "'.$pic_id.'" limit 1');
			if($avatar > 0 )
			{
				$pic_abs_path = $Aconf['domain_url'].'data/userimg/avatar_origin/'.$avatar.'.jpg'; 
				echo '<script type="text/javascript">window.parent.hideLoading();window.parent.buildAvatarEditor("'.$pic_id.'","'.$pic_abs_path.'","photo");</script>'; 
			}  
		} 
	}//if($o == 'i')
	//修改头像 end
	//我参与的评论 start
	if($o == 'c')
	{ 
		//1/2/3/4 文章/产品/专题/对商家留言*/
		$Acoms_type = array(1=>'新闻',2=>'商品',3=>'专题',4=>'商家');

		$where = $t?' and coms_type="'.$t.'"':'';   
		$count = $oPub->getOne('SELECT COUNT(*) as count from '.$pre.'users_comms WHERE users_id  = "'.$_SESSION['user_id'].'"'.$where); 
		$page = new ShowPage;  
		$page->PageSize = $Aconf['set_pagenum'];
		$page->PHP_SELF = PHP_SELF;
		$page->Total = $count; 
		$pagenew = $page->PageNum(); 
		$page->LinkAry = array('o'=>$o); 
		$strOffSet = $page->OffSet();
		/* 翻页 */
		$Ahome['users_comms_page'] = ($count> $Aconf['set_pagenum'])?$page->ShowLink_num():'';  

		$row = $oPub->select('SELECT *  FROM '.$pre.'users_comms WHERE users_id  = "'.$_SESSION['user_id'].'"'.$where.' order by dateadd desc limit '.$strOffSet); 
		while( @list( $k, $v ) = @each( $row ) ) { 
			//users_id, coms_type ,arid,dateadd,
			$row[$k]['dateadd'] = date("Y-m-d h:i",$v['dateadd']);
			$row[$k]['name_coms_type'] =  $v['coms_type']?'[<A HREF="user.php?o=c&t='.$v['coms_type'].'">'.$Acoms_type[$v['coms_type']].'</A>]':'';
			if($v['coms_type'] == 1) 
			{
				$row[$k]['name'] = $oPub->getOne("SELECT name FROM ".$pre."artitxt WHERE arid='".$v['arid']."' limit 1");  
				if($Aconf['rewrite']){ 
					$row[$k]['name_http'] =  'article-'.$v['arid'].'-0.html';  
				}else{
					$row[$k]['name_http'] =  'article.php?id='.$v['arid']; 
				} 

			}
			if($v['coms_type'] == 2)
			{
				$row[$k]['name'] = $oPub->getOne("SELECT name FROM ".$pre."producttxt WHERE prid='".$v['arid']."' limit 1");  
				if($Aconf['rewrite']){ 
					$row[$k]['name_http'] =  'product-'.$v['arid'].'.html';  
				}else{
					$row[$k]['name_http'] =  'product.php?id='.$v['arid']; 
				} 
			}
			if($v['coms_type'] == 4)
			{
				$row[$k]['name'] = $oPub->getOne("SELECT pra_name FROM ".$pre."pravail WHERE praid='".$v['arid']."' limit 1");  
				if($Aconf['rewrite']){ 
					$row[$k]['name_http'] =  'shop-'.$v['arid'].'.html';  
				}else{
					$row[$k]['name_http'] =  'shop.php?id='.$v['arid']; 
				} 
			} 
		}
		$Ahome['users_comms'] = $row;

	}
	//我参与的评论 end
	//地址管理 start
	if($o == 'addrs')
	{ 
		//地区分类 start
		if(!empty($ccid_5)){
			$ccid = $ccid_5;
		}elseif(!empty($ccid_4)){
			$ccid = $ccid_4;
		}elseif(!empty($ccid_3)){
			$ccid = $ccid_3;
		}elseif(!empty($ccid_2)){
			$ccid = $ccid_2;
		}elseif(!empty($ccid_1)){
			$ccid = $ccid_1;
		}
		$ccid = $ccid; 

		if($ccid > 0 && !empty($name) && !empty($tel) && !empty($addrs) && !empty($zip) )
		{
			$Afields=array('users_id'=>$_SESSION['user_id'],'ccid'=>$ccid,'addrs'=>$addrs,'zip'=>$zip,'name'=>$name,'tel'=>$tel,'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
			$id = $oPub->install($pre.'uaddrs',$Afields);
		}
		//地址列表
		$row = $oPub->select('SELECT *  FROM '.$pre.'uaddrs WHERE users_id  = "'.$_SESSION['user_id'].'"'); 
		while( @list( $k, $v ) = @each( $row ) )
		{  
			$Rrow = $oPub->getRow('SELECT fid,name FROM  '.$pre.'citycat where ccid='.$v['ccid'].' limit 1');
			if($Rrow['fid'] > 0)
			{
				$strccid = pre_node_orders($Rrow['fid'],$pre.'citycat','ccid');  
				$strccid =  $strccid.','.$v['ccid'];
			}else
			{
				$strccid = $v['ccid'];
			}
			$strname = '';
			$Rrow = $oPub->select('SELECT name FROM  '.$pre.'citycat where ccid in('.$strccid.')');
			while( @list( $key, $value ) = @each( $Rrow ) ) {
				$strname .=$value['name'].' ';
			}
			$row[$k]['ccid'] = $strname;
		}
		$Ahome['uaddrs'] = $row;unset($row);
		/* 城市列表 */
		$Acitycat["citycatOpt1"]=$Acitycat["citycatOpt2"]=$Acitycat["citycatOpt3"]=$Acitycat["citycatOpt4"] = '';

		$AnormAll = $oPub->select('SELECT * FROM '.$pre.'citycat where fid = 0'); 
		$Acitycat["citycatOpt0"] = '<SELECT NAME="ccid" onchange="selectsAjax(this.value,\'citycat\',\'show\',\'divccid\',1)">';
		$Acitycat["citycatOpt0"] .= '<OPTION VALUE="0" >选择地域分类</OPTION>';
		$n = 0;
		while( @list( $key, $value ) = @each( $AnormAll) ) {
			$n ++;
			$Acitycat["citycatOpt0"] .= '<OPTION VALUE="'.$value["ccid"].'" >'.$value["name"].'</OPTION>'; 
		}
		$Acitycat["citycatOpt0"] .= '</SELECT>';
		
		$Ahome['citycatOpt0'] = $Acitycat["citycatOpt0"];
		/* 城市列表 end */
	}
	//地址管理 end
	//汇款明细 start 
	if($o == 'detail')
	{   
		if( !empty($bankname) && !empty($remmoney) && !empty($payname) && !empty($paynums) )
		{
			$Afields=array('users_id'=>$_SESSION['user_id'],'bankname'=>$bankname,'remmoney'=>$remmoney,'payname'=>$payname,'paynums'=>$paynums,'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
			$id = $oPub->install($pre.'udetail',$Afields);
		}
 
		$where = '  users_id  = "'.$_SESSION['user_id'].'" and type < 1'; 
		$row = $oPub->getRow('SELECT COUNT(*) as count,sum(remmoney) as remmoney  FROM '.$pre.'udetail WHERE '. $where);  
		$Ahome['T_remmoney'] = $row['remmoney']; 
		$page = new ShowPage; 
		$page->PageSize = $Aconf['set_pagenum'];
		$page->PHP_SELF = PHP_SELF;
		$page->Total = $row[count];
		$pagenew = $page->PageNum();
		$page->LinkAry = array('o'=>$o); 
		$strOffSet = $page->OffSet();  
		$Ahome['showpage'] = ($row[count]  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
		$row = $oPub->select('SELECT *  FROM '.$pre.'udetail WHERE '.$where.'  order by id desc limit '.$strOffSet); 
		while( @list( $k, $v ) = @each( $row ) )
		{   
			$row[$k]['dateadd'] = date("Y-m-d H:i",$v['dateadd']); 
		}
		$Ahome['detail'] = $row;unset($row);
	}
	//汇款明细 end
	//收支明细 keep
	if($o == 'keep')
	{  

		$where = '  users_id  = "'.$_SESSION['user_id'].'"'; 
		$row = $oPub->getRow('SELECT COUNT(*) as count,sum(remmoney) as remmoney  FROM '.$pre.'udetail WHERE '. $where); 
		$Ahome['T_remmoney'] = $row['remmoney'];
		$page = new ShowPage; 
		$page->PageSize = $Aconf['set_pagenum'];
		$page->PHP_SELF = PHP_SELF;
		$page->Total = $row[count];
		$pagenew = $page->PageNum();
		$page->LinkAry = array('o'=>$o); 
		$strOffSet = $page->OffSet(); 
		
		$Ahome['showpage'] = ($row[count]  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
		$row = $oPub->select('SELECT *  FROM '.$pre.'udetail WHERE '.$where.'  order by id desc limit '.$strOffSet); 
		while( @list( $k, $v ) = @each( $row ) )
		{   
			$row[$k]['dateadd'] = date("Y-m-d H:i",$v['dateadd']); 
		}
		$Ahome['detail'] = $row;unset($row);
	}
	//收支明细 end

	//最新商品 start
	if($o == 'new')
	{   
		$where = ' shop_number>0  and  states<>1 AND   domain_id = "'.$Aconf['domain_id'].'"';
		$whereExt = ' and shop_number>0';
		$Ahome['products_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'producttxt WHERE '. $where);  
		$page = new ShowPage; 
		$page->PageSize = $Aconf['set_pagenum'];
		$page->PHP_SELF = PHP_SELF;
		$page->Total = $Ahome['products_count'];
		$pagenew = $page->PageNum();
		$page->LinkAry = array('o'=>$o); 
		$strOffSet = $page->OffSet(); 
		$Ahome['showpage'] = ($Ahome['products_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
		//排序
		$bys = 'up_date desc';
		$Ahome["products"] = products_list( $bys,"$strOffSet",'',$whereExt);  
	}
	//最新商品 end 
	//缺货商品 start
	if($o == 'qh')
	{   
		$where = ' shop_number < 1  and  states<>1 AND   domain_id = "'.$Aconf['domain_id'].'"';
		$whereExt = ' and  shop_number < 1 ';
		$Ahome['products_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'producttxt WHERE  '. $where);  
		$page = new ShowPage; 
		$page->PageSize = $Aconf['set_pagenum'];
		$page->PHP_SELF = PHP_SELF;
		$page->Total = $Ahome['products_count'];
		$pagenew = $page->PageNum();
		$page->LinkAry = array('o'=>$o); 
		$strOffSet = $page->OffSet(); 
		$Ahome['showpage'] = ($Ahome['products_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
		//排序
		$bys = 'up_date desc';
		$Ahome["products"] = products_list( $bys,"$strOffSet",'',$whereExt);  
	}
	//缺货 end
	//促销 star
	if($o == 'cx')
	{   
		$where = ' top>0 and shop_number>0  and  states<>1 AND   domain_id = "'.$Aconf['domain_id'].'"';
		$whereExt = ' and top>0 and shop_number>0 ';
		$Ahome['products_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'producttxt WHERE  '. $where);  
		$page = new ShowPage; 
		$page->PageSize = $Aconf['set_pagenum'];
		$page->PHP_SELF = PHP_SELF;
		$page->Total = $Ahome['products_count'];
		$pagenew = $page->PageNum();
		$page->LinkAry = array('o'=>$o); 
		$strOffSet = $page->OffSet(); 
		$Ahome['showpage'] = ($Ahome['products_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
		//排序
		$bys = 'up_date desc';
		$Ahome["products"] = products_list( $bys,"$strOffSet",'',$whereExt);  
	}
	//促销 end
	//畅销 start
	if($o == 'special')
	{   
		$where = ' special>0  and shop_number>0  and  states<>1 AND   domain_id = "'.$Aconf['domain_id'].'"';
		$whereExt = ' and special>0 and shop_number>0 ';
		$Ahome['products_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'producttxt WHERE '. $where);  
		$page = new ShowPage; 
		$page->PageSize = $Aconf['set_pagenum'];
		$page->PHP_SELF = PHP_SELF;
		$page->Total = $Ahome['products_count'];
		$pagenew = $page->PageNum();
		$page->LinkAry = array('o'=>$o); 
		$strOffSet = $page->OffSet(); 
		$Ahome['showpage'] = ($Ahome['products_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
		//排序
		$bys = 'up_date desc';
		$Ahome["products"] = products_list( $bys,"$strOffSet",'',$whereExt);  
	}
	//畅销 end 
	//sc 收藏管理 开始
	if($o == 'sc')
	{   
		$where = 'a.prid=b.prid and  a.users_id="'.$_SESSION['user_id'].'" and b.states<>1 AND   b.domain_id = "'.$Aconf['domain_id'].'"'; 
		$Ahome['products_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'producttxt as b,'.$pre.'ufv as a  WHERE '. $where);  
		$page = new ShowPage; 
		$page->PageSize = $Aconf['set_pagenum'];
		$page->PHP_SELF = PHP_SELF;
		$page->Total = $Ahome['products_count'];
		$pagenew = $page->PageNum();
		$page->LinkAry = array('o'=>$o); 
		$strOffSet = $page->OffSet(); 
		$Ahome['showpage'] = ($Ahome['products_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
		//排序
		$orderby = 'a.id desc';
		$rowproduct = $oPub->select('SELECT  b.prid,b.name, b.shop_sn, b.shop_number, b.shop_price,b.s_discount, b.min_thumb, b.shop_thumb FROM '.$pre.'producttxt as b,'.$pre.'ufv as a    
			WHERE '. $where.' ORDER BY  '.$orderby . ' limit '.$strOffSet);  
		if($rowproduct )
		foreach ($rowproduct AS $key=>$val) { 
			$rowproduct[$key]['alt_name'] = $val['name']; 
			if(!$val['shop_thumb']){
				$rowproduct[$key]['shop_thumb'] = 'images/command/no_imgsbig.png';
			}	
 
			$rowproduct[$key]['shop_price'] = ($val['shop_price'] == '0.00')?'':$val['shop_price']; 
			$rowproduct[$key]['s_discount'] = ($val['s_discount'])?$val['s_discount']:$rowproduct[$key]['shop_price'];

			if($Aconf['rewrite']){
				$rowproduct[$key]['product_url'] = 'product-'.$val['prid'].'.html'; 
			}else{
				$rowproduct[$key]['product_url'] = 'product.php?id='.$val['prid']; 
			} 
		}  

		$Ahome["products"] = $rowproduct;  
	}
	//sc end
	//购物车 start
	if($o == 'car')
	{   
		//清空购物车
		if($del=='all')
		{
			$oPub->query( 'delete from '.$pre.'carts where users_id="'.$_SESSION['user_id'].'" AND domain_id = "'.$Aconf['domain_id'].'"' );
			unset($del);
		}
		//提交订单 start
		if($ding=='yes')
		{
			$strMessages = '';
			if($uaddrs < 1)
			{
				$strMessages = '您还没有确认收货地址？';
			}else
			{ 
				//判断数量  
				if(count($prids) > 0)
				{
					$allowcar = true;
					foreach ($prids AS $prid => $nums)
					{
						$x = $oPub->getRow('SELECT name,shop_number,shop_price FROM '.$pre.'producttxt WHERE prid = '.$prid);
						if($x['shop_number'] < $nums)
						{
							$strMessages = $x['name'].' 超过库存数量，请重新确认';
							$allowcar = false;
							break;
						} 
					}
					
					//修改购物车状态
					if($allowcar)
					{
						$ddid = $_SESSION['user_id'].date("YmdHis");  
						$pronums = 0;
						$totalmoney = 0;
						foreach ($prids AS $prid => $nums)
						{
							$pronums = $pronums + $nums;
							$x = $oPub->getRow('SELECT sellprice,prices,dateadd  FROM '.$pre.'carts WHERE users_id = '.$_SESSION['user_id'].' and prid="'.$prid.'"'); 
							$tmpprice = $nums * $x['prices'];
							$totalmoney = $totalmoney + $tmpprice;
							$Afields=array('users_id'=>$_SESSION['user_id'],'prid'=>$prid,'nums'=>$nums,'sellprice'=>$x['sellprice'],'prices'=>$x['prices'],'totalprice'=>$tmpprice,'dateadd'=>$x['dateadd'],'ddid'=>$ddid,'domain_id'=>$Aconf['domain_id']);
							$oPub->install($pre.'ddscarts',$Afields);  
						}
						$oPub->query( 'delete from '.$pre.'carts where users_id="'.$_SESSION['user_id'].'" AND domain_id = "'.$Aconf['domain_id'].'"' ); 
						//添加到 订单列表  
						//收货地址 $uaddrs
						$v = $oPub->getRow('SELECT *  FROM '.$pre.'uaddrs WHERE users_id  = "'.$_SESSION['user_id'].'" and id="'.$uaddrs.'" limit 1');  
						$Rrow = $oPub->getRow('SELECT fid,name FROM  '.$pre.'citycat where ccid='.$v['ccid'].' limit 1');
						if($Rrow['fid'] > 0)
						{
							$strccid = pre_node_orders($Rrow['fid'],$pre.'citycat','ccid');  
							$strccid =  $strccid.','.$v['ccid'];
						}else
						{
							$strccid = $v['ccid'];
						}
						$strname = '';
						$Rrow = $oPub->select('SELECT name FROM  '.$pre.'citycat where ccid in('.$strccid.')');
						while( @list( $key, $value ) = @each( $Rrow ) ) {
							$strname .=$value['name'].' ';
						}
						$sh_address = $strname.$v['addrs'];
						$sh_zip     = $v['zip'];
						$sh_phone   = $v['tel'];
						$sh_name    = $v['name']; 

						$Afields=array('ddid'=>$ddid,'users_id'=>$_SESSION['user_id'],'pronums'=>$pronums,'totalmoney'=>$totalmoney,'time'=>gmtime(),'sh_name'=>$sh_name,'sh_address'=>$sh_address,'sh_zip'=>$sh_zip,'sh_phone'=>$sh_phone,'domain_id'=>$Aconf['domain_id']);
						$oPub->install($pre.'dds',$Afields);  
						header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
						header("Cache-Control: no-cache, must-revalidate");
						header("Pragma: no-cache"); 
						header("Location:user.php?o=ding&ddid=".$ddid);
						exit;

					}

				}else
				{
					$strMessages = '购物车里没商品？';
				} 
			}

		}
		//提交订单 end

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

		//收货地址列表
		$row = $oPub->select('SELECT *  FROM '.$pre.'uaddrs WHERE users_id  = "'.$_SESSION['user_id'].'"'); 
		while( @list( $k, $v ) = @each( $row ) )
		{  
			$Rrow = $oPub->getRow('SELECT fid,name FROM  '.$pre.'citycat where ccid='.$v['ccid'].' limit 1');
			if($Rrow['fid'] > 0)
			{
				$strccid = pre_node_orders($Rrow['fid'],$pre.'citycat','ccid');  
				$strccid =  $strccid.','.$v['ccid'];
			}else
			{
				$strccid = $v['ccid'];
			}
			$strname = '';
			$Rrow = $oPub->select('SELECT name FROM  '.$pre.'citycat where ccid in('.$strccid.')');
			while( @list( $key, $value ) = @each( $Rrow ) ) {
				$strname .=$value['name'].' ';
			}
			$row[$k]['ccid'] = $strname;
		}
		$Ahome['uaddrs'] = $row;unset($row); 
	}
	//购物车 end
	//订单列表 start
	if($o == 'ding')
	{   
		//删除订单 start
		if($so == 'del' && $id > 0)
		{
			$ddid = $oPub->getOne('SELECT ddid FROM  '.$pre.'dds where id="'.$id.'" and stats < 1 and users_id="'.$_SESSION['user_id'].'" limit 1'); 
			if(!empty($ddid))
			{
				$oPub->query('delete from '.$pre.'dds where  id="'.$id.'"' );
				$oPub->query('delete from '.$pre.'ddscarts where users_id="'.$_SESSION['user_id'].'" AND ddid="'.$ddid.'" and domain_id = "'.$Aconf['domain_id'].'"' ); 
			} 
		}
		//删除订单 end
		//确认订单
		if($ding_ok == 'yes' && $id > 0)
		{ 
			//检测totalmoney wlpay
			$Rdds = $oPub->getROW('SELECT ddid,totalmoney,wlpay FROM  '.$pre.'dds where id="'.$id.'" and stats < 1 and users_id="'.$_SESSION['user_id'].'" limit 1');
			$totalmoney = $Rdds['totalmoney'] + $Rdds['wlpay'];  
			$totalprice = $oPub->getOne('SELECT sum(totalprice) as totalprice FROM '.$pre.'ddscarts WHERE ddid="'.$Rdds['ddid'].'"');  
			if($totalmoney == $totalprice)
			{
				$users = $oPub->query('UPDATE '.$pre.'users set money=money-'.$totalmoney.' WHERE  id="'.$_SESSION['user_id'].'" limit 1');
				if($users)
				{
					$oPub->query('UPDATE '.$pre.'dds set stats= 1 WHERE  id="'.$id.'" limit 1'); 
					//记录支付信息
					$user_name = $oPub->getOne('SELECT user_name FROM  '.$pre.'users where id="'.$_SESSION['user_id'].'" limit 1'); 
					$Afields=array('users_id'=>$_SESSION['user_id'],'type'=>1,'bankname'=>'订单付款','remmoney'=>'-'.$totalmoney,'payname'=>$user_name,'paynums'=>$Rdds['ddid'],'dateadd'=>gmtime(),'checkdesc'=>$checkdesc,'domain_id'=>$Aconf['domain_id']);
					$oPub->install($pre.'udetail',$Afields); 
					$strMessages = '支付成功！'; 
				}else
				{
					$strMessages = '支付有失败，请重试！';
				} 
			}else
			{
				$strMessages = '金额不匹配，不能确认！';
			}
			$so = false; $id = false;

		}

		if($so == 'show' && $id > 0 && $ding_dhok == 'yes')
		{
			$oPub->query('UPDATE '.$pre.'dds set stats=3 WHERE  id="'.$id.'" and stats=2 limit 1'); 
			//已经到货
		} 
		//显示订单详情 start  
		if($so == 'show' && $id > 0 && $ding_ok <> 'yes')
		{
			$Rdds = $oPub->getRow('SELECT * FROM  '.$pre.'dds where id="'.$id.'" and users_id="'.$_SESSION['user_id'].'" limit 1'); 
			if($Rdds['id'] > 0)
			{
				//订单列表
				//$oPub->query( 'select * from '.$pre.'ddscarts where  ddid="'.$ddid.'" 
				$row = $oPub->select('SELECT *  FROM '.$pre.'ddscarts WHERE ddid="'.$Rdds['ddid'].'"');  
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
				$Rdds['ddscarts'] = $row; unset($row); 
				
				if($Rdds['stats']<1)
				{
					$Rdds['statsMessages'] = '未付款'; 
				}elseif($Rdds['stats']==1)
				{
					$Rdds['statsMessages'] = '已付款';
				}elseif($Rdds['stats']==2)
				{
					$Rdds['statsMessages'] = '已发货';
				}elseif($Rdds['stats']==3)
				{
					$Rdds['statsMessages'] = '已到货';
				} 
			}
			$Ahome['showdds'] = $Rdds; 
			//显示订单详情 end 
		} else
		{
			
			$where = ' users_id="'.$_SESSION['user_id'].'" AND   domain_id = "'.$Aconf['domain_id'].'"'; 
			$Ahome['ding_count'] = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'dds WHERE  '. $where);  
			$page = new ShowPage; 
			$page->PageSize = $Aconf['set_pagenum'];
			$page->PHP_SELF = PHP_SELF;
			$page->Total = $Ahome['ding_count'];
			$pagenew = $page->PageNum();
			$page->LinkAry = array('o'=>$o); 
			$strOffSet = $page->OffSet(); 
			$Ahome['showpage'] = ($Ahome['ding_count']  > $Aconf['set_pagenum'])?$page->ShowLink_num():''; 
			$row = $oPub->select('SELECT id,ddid,stats,pronums,totalmoney, time,sh_name,sh_address,sh_zip,sh_phone  FROM '.$pre.'dds WHERE '.$where.' order by id desc limit '.$strOffSet); 
			//ddid,stats,pronums,totalmoney, time,sh_name,sh_address,sh_zip,sh_phone
			while( @list( $k, $v ) = @each( $row ) )
			{ 
				if($v['stats']<1)
				{
					$row[$k]['statsMessages'] = '未付款'; 
				}elseif($v['stats']==1)
				{
					$row[$k]['statsMessages'] = '已付款';
				}elseif($v['stats']==2)
				{
					$row[$k]['statsMessages'] = '已发货';
				}elseif($v['stats']==3)
				{
					$row[$k]['statsMessages'] = '已到货';
				} 
				$row[$k]['time'] = date("Y-m-d H:i",$v['time']); 
			}//while( @list( $k, $v ) = @each( $row ) )
			$Ahome['ddid'] = $row;
		}
	} 
	//订单列表 end
}
//用户已经登录 end 
//用户邮箱验证 start
if($o == 'emailcheck' && $id > 0 && !empty($check))
{ 
	$Urow = $oPub->getRow('SELECT user_id,email,estats FROM '.$pre.'usersverify WHERE  user_id="'.$id.'"  limit 1'); 
	if($Urow['user_id'] > 0)
	{
		if($Urow['estats'] < 1 )
		{
			if($check == md5($Urow['email']))
			{
				$oPub->query('UPDATE '.$pre.'usersverify set estats= 1 WHERE  user_id="'.$id.'"  and domain_id="'.$Aconf['domain_id'].'"  limit 1');
				$strMessage = '已通过邮箱验证！';
			}else
			{
				$strMessage = '验证码错误，重新验证';
			}
 
		}else
		{
			$strMessage = '已通过验证，不需要重复！';
		}
	}else
	{
		$strMessage = '没有此邮箱记录！';
	}  
}
//用户邮箱验证 end
$Ahome['strMessage'] = $strMessage;

 
/* 调用模板 */
 
 
include_once( ROOT_PATH."includes/item_set.php"); 
$Aconf['header_title'] = $Aweb_url['user'][0]."|".$Aconf["web_title"];  

$Ahome["nowNave"]  = '<li><A HREF="./">homepage 首页</A>'.$Aconf['nav_symbol'].'</li><li><A HREF="'.$Aweb_url['user'][1].'">'.$Aweb_url['user'][0].'</a>'.$Aconf['nav_symbol'].'</li><li><A HREF="'.PHP_SELF.'?o='.$o.'">'.$Aop[$o].'</A></li>'; 
$Aconf['header_title'] = $Aop[$o].'|'.$Aweb_url['user'][0].'|'.$Aconf['header_title'];  

assign_template($Aconf); 
$smarty->assign('home', $Ahome ); 
$smarty->assign('user', $_SESSION ); 
unset($Ahome); 
 
$smarty->display($Aconf["displayFile"], $cache_id);

?>
