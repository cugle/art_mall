<?php
define('IN_OUN', true); 
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

define('ROOT_PATH', str_replace('avatar/camera.php', '', str_replace('\\', '/', __FILE__)));
define('DOCUMENT_ROOT', str_replace('\\','/',strtolower($_SERVER['DOCUMENT_ROOT']))); 
$ROOT_PATH     = ROOT_PATH;
$DOCUMENT_ROOT = DOCUMENT_ROOT; 

if(empty($DOCUMENT_ROOT)){
	$boardurl = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].preg_replace("/\/+(api|archiver|wap)?\/*$/i", '', substr( PHP_SELF, 0, strrpos( PHP_SELF, '/'))).'/');
	$Aboardurl = explode("/",$boardurl); 
	$SUBPATH = $Aboardurl[count($Aboardurl)-3];
} else
{
	$SUBPATH = (substr($DOCUMENT_ROOT,-1) == '/')?str_replace($DOCUMENT_ROOT, '', strtolower($ROOT_PATH)):str_replace($DOCUMENT_ROOT.'/', '',strtolower($ROOT_PATH));
} 

include_once( $ROOT_PATH."data/config.inc.php");  
include_once( $ROOT_PATH."includes/cls_session.php");
include_once( $ROOT_PATH."includes/funcomm.php");
include_once( $ROOT_PATH."class/mydb.php");  
/* 初始化设置 */
@ini_set('memory_limit',          '128M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    1);
@ini_set('display_errors',        1); 
@ini_set('error_reporting',       1); 
 
if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
} 

$oPub = new mydb($dbhost,$dbuser,$dbpw,$dbname); 
$dbhost = $dbuser = $dbpw = $dbname = NULL;  

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

define('SD_ROOT',  $ROOT_PATH.'data/userimg/'); 
if($_SESSION['user_id'] < 1)
{
	die(' plase login!');
}

$pic_id = $_SESSION['user_id'];

//生成图片存放路径 
$new_avatar_path = 'avatar_origin/'.$pic_id.'.jpg';

//将POST过来的二进制数据直接写入图片文件.
$len = file_put_contents(SD_ROOT.'./'.$new_avatar_path,file_get_contents("php://input")); 

//原始图片比较大，压缩一下. 效果还是很明显的, 使用80%的压缩率肉眼基本没有什么区别
$avtar_img = imagecreatefromjpeg(SD_ROOT.$new_avatar_path);
imagejpeg($avtar_img,SD_ROOT.$new_avatar_path,80);
//nix系统下有必要时可以使用 chmod($filename,$permissions);

//log_result('图片大小: '.$len);


//输出新保存的图片位置, 测试时注意改一下域名路径, 后面的statusText是成功提示信息.
//status 为1 是成功上传，否则为失败.
$d = new pic_data();
$d->data->photoId = $pic_id;
$d->data->urls[0] = 'avatar/'.$new_avatar_path;
$d->status = 1;
$d->statusText = '上传成功!';

$msg = json_encode($d);

@header('Content-type: text/html; charset=utf-8');
@header("Expires: 0");
@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
@header("Pragma: no-cache");
echo $msg;
 
 
class pic_data
{
	 public $data;
	 public $status;
	 public $statusText;
	public function __construct()
	{
		$this->data->urls = array();
	}
}

?>