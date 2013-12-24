<?php
if (!defined('IN_OUN')) {
    die('Hacking attempt');
} 
if (__FILE__ == '') {
	die('Fatal error code: 0');
} 


if(PHP_VERSION < '4.1.0') {
	$_GET = &$HTTP_GET_VARS;
	$_POST = &$HTTP_POST_VARS;
	$_COOKIE = &$HTTP_COOKIE_VARS;
	$_SERVER = &$HTTP_SERVER_VARS;
	$_ENV = &$HTTP_ENV_VARS;
	$_FILES = &$HTTP_POST_FILES;
}

if (isset($_SERVER['PHP_SELF'])) {
	define('PHP_SELF', $_SERVER['PHP_SELF']);
} else 
{
	define('PHP_SELF', $_SERVER['SCRIPT_NAME']);
}

define('ROOT_PATH', str_replace('includes/command.php', '', str_replace('\\', '/', __FILE__)));
define('DOCUMENT_ROOT', str_replace('\\','/',strtolower($_SERVER['DOCUMENT_ROOT']))); 
$ROOT_PATH     = ROOT_PATH;
$DOCUMENT_ROOT = DOCUMENT_ROOT;  
if(empty($DOCUMENT_ROOT)){
	$boardurl = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].preg_replace("/\/+(api|archiver|wap)?\/*$/i", '', substr( PHP_SELF, 0, strrpos( PHP_SELF, '/'))).'/'); 
	$Aboardurl = explode("/",$boardurl); 
	//$SUBPATH = $Aboardurl[count($Aboardurl)-3];
	$SUBPATH='';
} else
{
	//$SUBPATH = (substr($DOCUMENT_ROOT,-1) == '/')?str_replace($DOCUMENT_ROOT, '', strtolower($ROOT_PATH)):str_replace($DOCUMENT_ROOT.'/', '',strtolower($ROOT_PATH));
	$SUBPATH='';
} 

if (!file_exists(ROOT_PATH . 'data/install.lock'))
{
    header("Location: ./install/index.php\n"); 
    exit;
}

include_once( $ROOT_PATH."data/config.inc.php"); 
include_once( $ROOT_PATH."includes/language.php");
include_once( $ROOT_PATH."includes/funcomm.php");
include_once( $ROOT_PATH."includes/cls_session.php");
include_once( $ROOT_PATH."includes/cls_json.php");
include_once( $ROOT_PATH."includes/mydb.php");  
/* 初始化设置 */
@ini_set('memory_limit',          '128M');
@ini_set('session.cache_expire',  172800);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    1);
@ini_set('display_errors',        1); 
@ini_set('error_reporting',       1);   

if (defined('DEBUG_MODE') == false) {
    define('DEBUG_MODE', 0);
}  

foreach(array('_COOKIE', '_POST', '_GET') as $_request) {
	foreach($$_request as $_key => $_value) { 
		$_key{0} != '_' && $$_key = daddslashes($_value); 
	}
}  

if (PHP_VERSION >= '5.1' && !empty($timezone)) {
    date_default_timezone_set($timezone);
} 

$oPub = new mydb($dbhost,$dbuser,$dbpw,$dbname); 
$dbhost = $dbuser = $dbpw = $dbname = NULL;  

/* 通过用户输入的域名取得网站配置信息 */
$havedomin = FALSE; 
$_SERVER['SERVER_NAME'] = ($_SERVER['SERVER_PORT'] != 80)?$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT']:$_SERVER['HTTP_HOST'];
if($Aconf['allow_home']) {
	$sql = 'SELECT * FROM '.$pre.'sysconfigfast WHERE main_domin="'.$_SERVER['SERVER_NAME'].'" AND states <> 1 ORDER BY scid ASC LIMIT 1';
}else {
	$sql = 'SELECT * FROM '.$pre.'sysconfigfast WHERE scid="'.$Aconf['allow_home'].'" ORDER BY scid ASC  limit 1';
}
$Anorm = $oPub->getRow($sql); 
if($Anorm) {  
    $Aconf['domain_url']  = 'http://'.$Anorm['main_domin'].'/'.$SUBPATH; 
	$havedomin = true;
} else if(strpos($_SERVER['SERVER_NAME'],$Aconf['mail_url'] ) )
{
    $user_name = str_replace('.'.$Aconf['mail_url'],'',$_SERVER['SERVER_NAME']);
    $Anorm = $oPub->getRow('SELECT * FROM '.$pre.'sysconfigfast WHERE user_name="'.$user_name.'" AND states <> 1 ORDER BY scid ASC LIMIT 1'); 
	if($Anorm) { 
	    $Aconf['domain_url']  = 'http://'.$Anorm['user_name'].'.'.$Aconf['mail_url'].'/'.$SUBPATH;
		$_SERVER['SERVER_NAME'] = $Anorm['user_name'].'.'.$Aconf['mail_url'];
		$havedomin = true;
	}
} else if($_SERVER['SERVER_NAME'] == $Aconf['mail_url'])
{ 
    $Anorm = $oPub->getRow('SELECT * FROM '.$pre.'sysconfigfast WHERE main_domin="www.'.$_SERVER['SERVER_NAME'].'" AND states <> 1 ORDER BY scid ASC LIMIT 1'); 
	if($Anorm) {
		$Aconf['domain_url']  = 'http://'.$_SERVER['SERVER_NAME'].'/'.$SUBPATH;
		$havedomin = true;
	}
} 

if(!$havedomin ) { 
   $strShow = showMessage($Aweb_desc["sys_url_error"],$Aweb_desc["sys_url_title"]);
   echo $strShow; exit;
} else {
	/* 基本配置信息 start*/
	$Anorm = $oPub->getRow('SELECT * FROM '.$pre.'sysconfig WHERE scid="'.$Anorm['scid'].'" LIMIT 1'); 
	$Anorm['domain_id']   = $Anorm['scid'] ;
	$Anorm['web_title']   = $Anorm['header_title'] ;
	$Anorm['domain_url']  = 'http://'.$Anorm['main_domin'].'/'.$SUBPATH;   

	$Asets = explode("{|}",$Anorm['sets']);
	while( @list( $k, $v ) = @each( $Asets) ) 
	{ 
		$At = array();
		$At = explode("[|]",$v);
		if($At[0]) {
		   if($At[0] == 'tongji') {
				$At[1] = base64_decode($At[1]);
				$At[1] = str_replace('\"','"',$At[1]);
			}
		   $Anorm[$At[0]] = $At[1]; 
		}
	} 
	$Anorm['mail_url']      = $Aconf['mail_url']; 
	$Anorm['allow_home']    = $Aconf['allow_home'];  
	$Anorm['now_url']		= basename(PHP_SELF); 
	if($Anorm['logo']) {
		$Atmp = explode('.',$Anorm['logo']);
        if($Atmp[1] == 'swf') { 
			$Aconf['logo'] = '<script type="text/javascript">';
			$Aconf['logo'] .= 'document.write(\'<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
			$Aconf['logo'] .= 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="'.$Anorm['logo_w'].'" height="'.$Anorm['logo_h'].'">\');';
			$Aconf['logo'] .= 'document.write(\'<param name="movie" value="data/weblogo/'.$Anorm['logo'].'"><param name="quality" value="high">\');';
			$Aconf['logo'] .= 'document.write(\'<param name=wmode value="transparent">\');';
			$Aconf['logo'] .= 'document.write(\'<embed src="data/weblogo/'.$Anorm['logo'].'"  quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer"'; 
			$Aconf['logo'] .= 'type="application/x-shockwave-flash"   wmode="transparent" width="'.$Anorm['logo_w'].'" height="'.$Anorm['logo_h'].'"></embed>\');';
			$Aconf['logo'] .= 'document.write(\'</object>\');';
			$Aconf['logo'] .= '</script>'; 
		} else 
		{
           $Aconf['logo'] = '<IMG SRC="'.$Anorm['domain_url'].'data/weblogo/'.$Anorm['logo'].'" BORDER="0" title="'.$Anorm['header_title'].'" width="'.$Anorm['logo_w'].'" height="'.$Anorm['logo_h'].'">';
		}
	} else 
	{
        $Aconf['logo'] = false;
	}
	$Anorm['logo']          = $Aconf['logo']; 
	$Anorm['cache_time']    = $Anorm['cache_time']?$Anorm['cache_time']:0;  
	$Anorm['template'] = empty($Anorm['template'])?'business':$Anorm['template']; 
	$Anorm['template']      = ($Anorm['user_template'] > 0 )?'../templates/user_themes/'.$Anorm['scid'].'/':$Anorm['template'].'/';
	$Anorm['template_path'] = ($Anorm['user_template'] > 0 )?'templates/user_themes/'.$Anorm['scid'].'/':'themes/'.$Anorm['template']; 
	$Anorm['template_base'] = ($Anorm['user_template'] > 0 )?'templates/user_themes/'.$Anorm['scid'].'/':'themes/'.$Anorm['template'];  
	$Anorm['notices_clean'] = clean_html($Anorm['notices']);
	//main_web messages  
	$row = $oPub->getRow('SELECT user_id,main_domin,header_title,sets FROM '.$pre.'sysconfig WHERE scid="'.$Aconf['allow_home'].'" limit 1');  
	$Anorm['admin_main_domin'] = 'http://'.$row['main_domin'].'/'.$SUBPATH;
	$Anorm['admin_web_title'] = $row['header_title'];
	$Anorm['admin_user_id'] = $row['user_id']; 
	$Asets = explode("{|}",$row['sets']);
	while( @list( $k, $v ) = @each( $Asets) )
	{ 
		$At = array();
		$At = explode("[|]",$v);
		if($At[0]){
		   $Anorm['admin_'.$At[0]] = $At[1]; 
		}
	}  

	$Aconf['set_pagenum'] = 30; //列表数量
	$Aconf = array_merge($Anorm,$Aconf); 
	$Aconf['domain_user_id'] = $Aconf['user_id'];
	unset($Anorm);unset($row); 
}  
$Aconf['preFile'] = preg_replace (array("'/".$SUBPATH."'","'.php'","'admin'"),array("","",""),PHP_SELF);  
$Aconf['nowFile'] = $Aconf['rewrite'] ?$Aconf['preFile'].".html":$Aconf['preFile'].".php";  
$Aconf['displayFile'] = $Aconf['preFile'].".html"; 

if (is_spider()) {
    /* 如果是蜘蛛的访问，那么默认为访客方式，并且不记录到日志中 */
    if (!defined('INIT_NO_USERS')) {
        define('INIT_NO_USERS', true);
    }
    $_SESSION = array();
	$_SESSION['user_id']     = 0; 
	$_SESSION['auser_id']   = 0; 
}
if($id > 0 || $arid > 0){
	$id = $id + 0;
	$arid  = $arid +0;
}
if (!defined('INIT_NO_USERS'))
{
	/* 初始化session */  
	include_once( $ROOT_PATH.'includes/cls_session.php'); 
	$session_name =  !isset($admin_path)?'oun_id':'aoun_id'; 
	$sess = new cls_session($oPub, $pre.'sessions', $pre.'sessions_data',$session_name);  
	define('SESS_ID', $sess->get_session_id());  
	include_once( $ROOT_PATH.'includes/osunit.php');
	$user = new osunit($oPub, $pre.'users');   
}
if (!defined('INIT_NO_SMARTY')) {
    header('Cache-control: private');
    header('Content-type: text/html; charset=utf-8'); 
    /* 创建 Smarty 对象。*/
    require($ROOT_PATH . 'includes/cls_template.php');
    $smarty = new cls_template; 
 
	$smarty->cache_lifetime = $Aconf['cache_time']; 
    $smarty->template_dir   = $ROOT_PATH . 'themes/' . $Aconf['template']; 
	
	$base_chachereg_dir     = $ROOT_PATH . 'templates/caches/';
	$base_compilereg_dir    = $ROOT_PATH . 'templates/compiled/';
	$base_chache_dir        = $ROOT_PATH . 'templates/caches/'.$Aconf['domain_id'];
	$base_compile_dir       = $ROOT_PATH . 'templates/compiled/'.$Aconf['domain_id'];
	if(strpos(PHP_SELF,$Aconf['manage_dir'] ) ) {
		//后台
		$base_chache_dir   = $base_chache_dir.'/'.$Aconf['manage_dir'];
		$base_compile_dir  = $base_compile_dir.'/'.$Aconf['manage_dir'];
		if (!file_exists($base_chache_dir)) {
			if(!make_dir($base_chache_dir)) { 
				$strMessage = $Aweb_desc["sys_dir_chmod"].$base_chache_dir;
			    echo  showMessage($strMessage); exit; 
			} 
		} 

		if (!file_exists($base_compile_dir)) {
			if(!make_dir($base_compile_dir)) { 
				 $strMessage =  $Aweb_desc["sys_dir_chmod"].$base_compile_dir;
				 echo  showMessage($strMessage); exit; 
			} 
		} 
		$Aconf['rewrite'] = false; //后台不用伪静态
		
	}else 
	{ 
		//前台：新闻分类、新闻最终页 新闻评论页 商品分类、商品最终页 缓存子目录  
		if(in_array($Aconf['preFile'] ,array('articles','article','acomms','products','product','procomms')) && $id > 0) {  
			//如果是新闻文章加上新闻自己的年份，加上年为子目录
			if(in_array($Aconf['preFile'] ,array('articles','products'))) {
				$dir_cache_id = $id;  
			} 
			if(in_array($Aconf['preFile'] ,array('article','acomms'))) { 
				$row = $oPub->getrow('SELECT acid,dateadd FROM  '.$pre.'artitxt WHERE arid = "'.$id.'" LIMIT 1');  
				$dir_cache_id = $row['acid'].'/'.date("Y",$row['dateadd']);  
			}  
			if(in_array($Aconf['preFile'] ,array('product','procomms'))) { 
				$row = $oPub->getrow('SELECT pcid,dateadd FROM  '.$pre.'producttxt WHERE prid = "'.$id.'" LIMIT 1');  
				$dir_cache_id = $row['pcid'].'/'.date("Y",$row['dateadd']);  
			} 

			$base_chache_dir = $base_chache_dir.'/'.$dir_cache_id; 
			if (!file_exists($base_chache_dir)) {
				if(!make_dir($base_chache_dir)) {  
					 $strMessage =  $Aweb_desc["sys_dir_chmod"].$base_chache_dir;
					 echo  showMessage($strMessage); exit; 
				} 
			} 
		} 
	} 
	//end 
    $smarty->cache_dir      = $base_chache_dir;
    $smarty->compile_dir    = $base_compile_dir; 
    if ((DEBUG_MODE & 2) == 2) {
        $smarty->direct_output = true;
        $smarty->force_compile = true;
    }  else {
        $smarty->direct_output = false;
        $smarty->force_compile = false;
    }
	//如果提交留言  强制更新缓存 
	if($act == 'install' || strpos(PHP_SELF,$Aconf['manage_dir'] ) ){
		$smarty->direct_output = true;
        $smarty->force_compile = false;
	} 
} 

$Ahome['users'] = false;  
  
if ( empty($_SESSION['user_id']) ) {  
	if ($user->get_cookie()) {  
		if ($_SESSION['user_id'] > 0) {  
			$oPub->query('delete FROM '.$pre.'sessions where userid='.$_SESSION['user_id'].' and sesskey <> "'.SESS_ID.'"'); 
			update_user_info(); 
			$Ahome['users'] = $user->get_profile_by_id($_SESSION['user_id']); 
			if(strpos(PHP_SELF,$Aconf['manage_dir'] ))
			{
				$ifmanger_id = $oPub->getOne("SELECT id  FROM ".$pre."users WHERE  ifmanger=1 AND id = '".$_SESSION['user_id']."' limit 1");
				if($ifmanger_id > 0)
				{
					$Ax = $oPub->getRow("SELECT user_id,user_name,action_list,articlecat_list,praid  FROM ".$pre."admin_user WHERE user_id = '".$_SESSION['user_id']."'   and domain_id  = '".$Aconf['domain_id']."' limit 1");   
					set_admin_session($Ax['user_id'],$Ax['user_name'],$Ax['action_list'],$Ax['articlecat_list'],$Ax['praid'],$Aconf['domain_url'],$Aconf['domain_id'],$Aconf['domain_user_id']);  
					unset($Ax); 
				}
			}
		}
	} else {
		 $_SESSION['user_id']      = 0;
		 $_SESSION['auser_id']     = 0;
	} 
}  
$Aconf['nav_symbol'] = ' >';
//没有成功登陆则退出   
/* 判断是否支持gzip模式 */ 
$pos = strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache');
if ($pos === false) { 
} else { 
   if (gzip_enabled()) { 
	   ob_start("compress");
   } else {
	   ob_start();
   } 
} 
?>
