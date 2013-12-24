<?php
define('IN_OUN', true); 
error_reporting(E_ALL ^ E_NOTICE);
header('Content-type: text/html; charset=utf-8');
/**
* ----------------------------------------------------------------------------
* osunit   版权所有xufyong@gmail.com，并保留所有权利。
* 网站地址: http://www.osunit.com
* ----------------------------------------------------------------------------
* 这是一个免费开源的软件；您可以在不用于商业目的的前提下对程序代码
* 进行修改、使用和再发布。
* 技术支持,合作联系：QQ:16953292 msn:xufyong@gmail.com
*/

if (__FILE__ == '')
{
    die('Fatal error code: 0');
} 
/* 环境检测页 */
$_LANG['checking_title'] = 'OSUNIT安装程序 第2步/共3步 环境检测';
$_LANG['system_environment'] = '系统环境';
$_LANG['dir_priv_checking'] = '目录权限检测';
$_LANG['template_writable_checking'] = '模板可写性检查';
$_LANG['rename_priv_checking'] = '特定目录修改权限检查';
$_LANG['welcome_page'] = '欢迎页';
$_LANG['recheck'] = '重新检查';
$_LANG['config_system'] = '配置系统';
$_LANG['does_support_mysql'] = '是否支持MySQL';
$_LANG['support'] = '支持';
$_LANG['does_support_dld'] = '重要文件是否完整';
$_LANG['support_dld'] = '完整';
$_LANG['support'] = '支持';
$_LANG['not_support'] = '不支持';
$_LANG['cannt_support_dwt'] = '缺少dwt文件';
$_LANG['cannt_support_lbi'] = '缺少lib文件';
$_LANG['cannt_support_dat'] = '缺少dat文件';
$_LANG['php_os'] = '操作系统';
$_LANG['php_ver'] = 'PHP 版本';
$_LANG['mysql_ver'] = 'MySQL 版本';
$_LANG['gd_version'] = 'GD 版本';
$_LANG['jpeg'] = '是否支持 JPEG';
$_LANG['gif'] = '是否支持 GIF';
$_LANG['png'] = '是否支持 PNG';
$_LANG['safe_mode'] = '服务器是否开启安全模式';
$_LANG['safe_mode_on'] = '开启';
$_LANG['safe_mode_off'] = '关闭';
$_LANG['can_write'] = '可写';
$_LANG['cannt_write'] = '不可写';
$_LANG['not_exists'] = '不存在';
$_LANG['cannt_modify'] = '不可修改';
$_LANG['all_are_writable'] = '所有模板，全部可写';

/* 系统设置 */
$_LANG['setup'] = '填写完毕';
$_LANG['setting_title'] = 'OSUNIT安装程序 第2步/共2步 配置系统';
$_LANG['db_account'] = '数据库帐号';
$_LANG['db_port'] = '端口号：';
$_LANG['db_host'] = '数据库主机：';
$_LANG['db_name'] = '数据库名：';
$_LANG['db_user'] = '用户名：';
$_LANG['db_pass'] = '密码：';
$_LANG['go'] = '搜';
$_LANG['db_list'] = '已有数据库';
$_LANG['db_prefix'] = '表前缀：';
$_LANG['change_prefix'] = '建议您修改表前缀';
$_LANG['admin_account'] = '管理员帐号';
$_LANG['admin_name'] = '管理员姓名：';
$_LANG['admin_password'] = '登录密码：';
$_LANG['admin_password2'] = '密码确认：';
$_LANG['admin_email'] = '电子邮箱：';


/* 提示信息 */
$_LANG['has_locked_installer'] = '<strong>安装程序已经被锁定。</strong><br /><br />如果您确定要重新安装 OSUNIT，请删除data目录下的 install.lock。';
$_LANG['connect_failed'] = '连接 数据库失败，请检查您输入的 数据库帐号 是否正确。';
$_LANG['query_failed'] = '查询 数据库失败，请检查您输入的 数据库帐号 是否正确。';
$_LANG['select_db_failed'] = '选择 数据库失败，请检查您输入的 数据库名称 是否正确。';
$_LANG['cannt_find_db'] = '无';
$_LANG['cannt_create_database'] = '无法创建数据库';
$_LANG['password_empty_error'] = '密码不能为空';
$_LANG['passwords_not_eq'] = '密码不相同';
$_LANG['open_config_failed'] = '打开配置文件失败';
$_LANG['write_config_failed'] = '写入配置文件失败';
$_LANG['create_passport_failed'] = '创建管理员帐号失败';
$_LANG['cannt_mk_dir'] = '无法创建目录';
$_LANG['cannt_copy_file'] = '无法复制文件';
$_LANG['open_installlock_failed'] = '打开install.lock文件失败';
$_LANG['write_installlock_failed'] = '写入install.lock文件失败';

$_LANG['install_done_title'] = 'OSUNIT 安装程序 安装成功';
$_LANG['install_error_title'] = 'OSUNIT 安装程序 安装失败';
$_LANG['done'] = '恭喜您，OSUNIT 已经成功地安装完成。<br />基于安全的考虑，请在安装完成后删除 install 目录。';
$_LANG['go_to_view_my_OSUNIT'] = '前往 OSUNIT 首页';
$_LANG['go_to_view_control_panel'] = '前往 OSUNIT 后台管理中心';
$_LANG['open_config_file_failed'] = '无法写入 config.inc.php，请检查该文件是否允许写入。';
$_LANG['write_config_file_failed'] = '写入配置文件出错';

/* 取得当前xy58所在的根目录 */
define('ROOT_PATH', str_replace('install/index.php', '', str_replace('\\', '/', __FILE__)));
define('DOCUMENT_ROOT', str_replace('\\','/',$_SERVER["DOCUMENT_ROOT"]));

$strDOCUMENT_ROOT =  strtolower(DOCUMENT_ROOT);
$strroot_path = strtolower(ROOT_PATH);

$SUBPATH = str_replace($strDOCUMENT_ROOT .'/', '', $strroot_path);
//include_once( "../config.inc.php");
include_once( ROOT_PATH."includes/mydb.php");
include_once( ROOT_PATH."includes/language.php");
include_once( "./lib_installer.php");
include_once( "./cls_sql_executor.php");

$_SERVER["SERVER_NAME"] = ($_SERVER["SERVER_PORT"] != 80)?$_SERVER["SERVER_NAME"].':'.$_SERVER["SERVER_PORT"]:$_SERVER["SERVER_NAME"];

$main_http = 'http://'.$_SERVER["SERVER_NAME"].'/'.$SUBPATH;

if (file_exists(ROOT_PATH . 'data/install.lock'))
{
    die('Please del "data/instal.lock" file!');
}

$step = isset($_REQUEST['step']) ? $_REQUEST['step'] : 'welcome';

switch ($step)
{
	case 'create_database' :
		/* 创建配置文件 */
		$db_host    = isset($_POST['db_host'])      ?   trim($_POST['db_host']) : '';
		$db_port    = isset($_POST['db_port'])      ?   trim($_POST['db_port']) : '';
		$db_user    = isset($_POST['db_user'])      ?   trim($_POST['db_user']) : '';
		$db_pass    = isset($_POST['db_pass'])      ?   trim($_POST['db_pass']) : '';
		$db_name    = isset($_POST['db_name'])      ?   trim($_POST['db_name']) : '';
		$prefix     = isset($_POST['db_prefix'])    ?   trim($_POST['db_prefix']) : '';

		 $mail_url  = $_SERVER["SERVER_NAME"];
		 $mail_url = str_replace('http://','', $mail_url);
		 $mail_url = str_replace('https://','',$mail_url);
		 $mail_url = str_replace('/','',$mail_url);
		 $Axy = explode("www.",$mail_url);
		 if(count($Axy) > 0) {
			 $mail_url_config =  str_replace("www.","",$mail_url); 
		 }else
		 {
			 $mail_url_config = $mail_url;
		 }
		$result = create_config_file($db_host, $db_port, $db_user, $db_pass, $db_name, $prefix,$mail_url_config );
		$strMessage = '安装状态';
		$strOk = '';
		if(!$result){
			$strOk = '配置文件创建失败';
		} else
		{
			$strOk = '配置文件创建成功';
			/* 创建数据库 */
			$result = create_database($db_host, $db_port, $db_user, $db_pass, $db_name);
			if ($result ==3 )
			{
				$strOk .= '<br/><br/><span style="color:#F00">数据库用户名、密码或端口号错误，数据库连接失败! <br/><br/>请确认数据库信息后重新安装。</span><br/><br/>';
				@unlink(ROOT_PATH."data/config.inc.php");
			}else if($result ==4)
			{
				$strOk .= '<br/><br/><span style="color:#F00">'.$db_name.' 数据库创建失败! <br/><br/>请确认数据库信息后重新安装。</span><br/><br/>';
				@unlink(ROOT_PATH."data/config.inc.php");
			}else if($result ==1)
			{
				$strOk .= '<br/><br/>'.$db_name.' 数据库创建成功!<br/><br/>';
				//导入数据表
				$data_path = ROOT_PATH .'install/incstar.sql';
				$sql_files = array(
					 ROOT_PATH .'install/structure.sql',
					 $data_path
				 ); 
				 $result = install_data($sql_files);
				 if($result) { 
					 $strOk .=' 初始数据导入成功<br/><br/>';
					 $strOk .=' <font color="FF0000">初始管理帐号:admin 密码：123456</font><br/><br/>';
					 $strOk .=' <A HREF="'.$main_http.'admin">请登录后修改密码</A>!<br/><br/>';

					 $strOk .='  请删除 install(安装目录) <br/><br/>';
					 @$fp = fopen(ROOT_PATH."data/install.lock","a");	 
					 @fclose($fp); 
				} 
			}else
			{
				$strOke .= '<br/><br/>数据库创建未知错误! <br/>请确认数据库信息后重新安装。</span><br/><br/>'; 
				@unlink(ROOT_PATH."data/config.inc.php");
			} 
		} 
		break; 
	default :
		include_once(ROOT_PATH . 'install/includes/lib_env_checker.php');
		include_once(ROOT_PATH . 'install/lib_installer.php');
		include_once(ROOT_PATH . 'install/includes/checking_dirs.php');
		$dir_checking = check_dirs_priv($checking_dirs); 
		
		$disabled = '';
		if ($dir_checking['result'] === 'ERROR' || !function_exists('mysql_connect'))
		{
			$disabled = 'disabled="true"';
		}

		$system_info = get_system_info(); 
		if($_GET["step"] == 'check'){
			$strMessage = '简单两步完成安装：1、检查安装环境；<span style="color:#FF3300">2、填写信息,执行安装。</span>'; 
		}else{ 
			$strMessage = '简单两步完成安装：<span style="color:#FF3300">1、检查安装环境；</span>2、填写信息,执行安装。'; 
		}
		//检查安装环境
} 
?>
<HTML lang=utf-8 xml:lang="utf-8" xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<TITLE>行业之星 安装程序</TITLE>
<META http-equiv=content-type content="text/html; charset=utf-8"> 
<LINK href="css/style.css" type=text/css rel=stylesheet>  
<style>
	.user_box{width:650px;margin:20px 0 0 100px;clear:both;border-style:solid; border-width:1px; border-color:#CCCCCC;padding: 20px 0 20px 0;}
	.user{width:200px;float:left;font-weight: bold;text-align: right;}
	.user div{margin: 5px} 
	li {list-style-type:none;}
</style>
</HEAD>
<BODY bgColor=#78879e>
<TABLE cellSpacing=0 cellPadding=0 width="99%" border=0>
  <TR>
    <TD width="1%"><IMG height=23 src="images/hbar_left.gif" width=10></TD>
    <TD align=middle width="99%" background=images/hbar_middle.gif>
      <DIV class=caption> 行业之星 <?php echo $Aconf['OSUNIT_VERSION'];?> 快速安装 </DIV>
	</TD>
    <TD width="1%"><IMG height=23 src="images/hbar_right.gif" width=10></TD>
 </TR>
</TABLE>


<DIV class="content">
<TABLE width="96%" border=0> 
  <TR class="odd" >
    <TD align="middle">
	<div class="shadow">
     <?php echo $strMessage;?>
	</div>
	 <div style="border-style:solid; border-width:1px; border-color:#E5E5E5;"></div>
    </TD>
  </TR>	 
  <?php if($_GET["step"] == 'check'){?>
	  <TR class="even">
		  <TD align="middle"> 
				<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self" style="margin: 0">
					<ul class="user_box"> 
						<li style="margin:5px;width:620px;">
							<div class="user">数据库地址:</div>
							<div style="width:400px;"><INPUT TYPE="text" NAME="db_host" value="localhost" size="16"></div>  
						</li> 	
						<li style="margin:5px;width:620px;">
							<div class="user">端口号:</div>
							<div style="width:400px;"><INPUT TYPE="text" NAME="db_port" value="3306" size="5"></div>  
						</li>
						<li style="margin:5px;width:620px;">
							<div class="user">数据库用户名:</div>
							<div style="width:400px;"><INPUT TYPE="text" NAME="db_user" id="db_user" value="" size="16"></div>  
						</li> 
						<li style="margin:5px;width:620px;">
							<div class="user">数据库密码:</div>
							<div style="width:400px;"><INPUT TYPE="text" NAME="db_pass" id="db_pass" value="" size="16"></div>  
						</li> 
						<li style="margin:5px;width:620px;">
							<div class="user">数据库名称:</div>
							<div style="width:400px;"><INPUT TYPE="text" NAME="db_name" value="osunit" size="16"></div>  
						</li> 
						<li style="margin:5px;width:620px;">
							<div class="user">&nbsp;</div>
							<div style="width:400px;">
								<INPUT TYPE="hidden" NAME="db_prefix" value="oun_" />
								<INPUT TYPE="submit" name="submit" value="确认安装" style="background-color: #FF9900;">
								<INPUT TYPE="hidden" name="step" value="create_database">
							</div>  
						</li> 					
					</ul>   
				</FORM> 
				<br/><br/>
			
		  </TD>
	  </TR> 
 <?php }elseif($_POST["step"] == 'create_database'){ ?>
	  <TR class="even">
		  <TD>
		     <div class="shadow" style="margin:20px 20px 20px 50px;font-size: 16px;font-weight:lighter " >
				<?php echo $strOk ;?>
				<A HREF="../">>>>></A>
			 </div>
			
		  </TD>
	  </TR> 	 
 	 
 <?php }else{ ?>
	  <TR class="even">
		  <TD>
			<div class="shadow"> 系统信息 </div>		
		  </TD>
	  </TR>
	  <TR class="odd">
		  <TD>
		  <TABLE width="80%" border="1" cellpadding="0" cellspacing="0">
		  <TR>
			  <TD align="left" width="30%"><B style="margin-left: 30px">名称</B></TD>
			  <TD align="middle"  width="30%"><B>状态</B></TD> 
			  <TD align="middle"  width="60%">&nbsp;</TD>
		  </TR>
			<?php  
	 
			foreach ($system_info AS $key=>$val) {  ?> 
				<TR>
				<TD align="left" width="30%"><span style="margin-left: 30px"><?php echo $val[0];?></span></TD> 
				<TD align="middle" width="30%"><?php echo $val[1];?></TD> 
				<td align="middle" width="40%">&nbsp;</TR>
			<?php } ?>
		  </TABLE>
		  </TD>
	  </TR> 

	  <TR class="even">
		  <TD>
			<div class="shadow"> 目录检查 </div>		
		  </TD>
	  </TR>
		
	  <TR class="odd">
		  <TD>
		  <TABLE width="80%" border="1" cellpadding="0" cellspacing="0">
		  <TR>
			  <TD align="left" width="30%"><B style="margin-left: 30px">目录名</B></TD>
			  <TD align="middle"  width="30%"><B>权限</B></TD> 
			  <td align="middle"  width="60%"><B>状态</B></td>
		  </TR>
			<?php  
			$Atmp = $dir_checking["detail"];
			foreach ($Atmp AS $key=>$val) {  ?> 
				<TR>
				<TD align="left" width="30%"><span style="margin-left: 30px"><?php echo $val[0];?></span></TD> 
				<TD align="middle" width="30%"><?php echo $val[1];?></TD> 
				<td align="middle" width="40%"><?php echo ($val[1]=='可写')?'<span style="color:#3300CC">通过</span>':'<span style="color:#FF3300">请修改</span>'?></td>
				</TR>
			<?php } ?>
		  </TABLE>
		  </TD>
	  </TR> 

	  <TR class="even">
		  <TD>
		  <div class="shadow"> 
			<?php 
			if($disabled ){
				echo '<A HREF="index.php" style="margin:20px;padding: 10px;color:#FF0000;font-size: 14px ">没有通过，请确认所列目录与文件为 777 权限。点击刷新,重新检查</A>';
			}else
			{
				echo '<A HREF="index.php?step=check" style="margin:40px;padding:20px;color:#00FF00;font-size: 24px;font-weight: bold ">下一步>></A>';
			}
			?> 
		  </div>
		  </TD>
	  </TR>
 <?php } ?>
</TABLE> 
</DIV>


<TABLE cellSpacing=0 cellPadding=0 width="99%" border=0>
  <TR>
    <TD width="1%"><IMG height=23 src="images/hbar_left.gif" width=10></TD>
    <TD align="center" width="99%" background="images/hbar_middle.gif">
      <DIV class="fontSmall"><?php echo $Aconf['footer_title'];?>
     </DIV></TD>
    <TD width="1%"><IMG height=23 src="images/hbar_right.gif" width=10></TD>
 </TR>
</TABLE>
</BODY></HTML>
