<?php 
define('SD_ROOT',  str_replace('avatar/save_avatar.php', '', str_replace('\\', '/', __FILE__)).'data/userimg/');
@header('Content-type: text/html; charset=utf-8');
@header("Expires: 0");
@header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
@header("Pragma: no-cache"); 

$type = isset($_GET['type'])?trim($_GET['type']):'small';
$pic_id = trim($_GET['photoId']);  
$new_avatar_path = 'avatar_'.$type.'/'.$pic_id.'_'.$type.'.jpg'; 
$len = file_put_contents(SD_ROOT.$new_avatar_path,file_get_contents("php://input")); 

$avtar_img = imagecreatefromjpeg(SD_ROOT.$new_avatar_path);
imagejpeg($avtar_img,SD_ROOT.$new_avatar_path,80); 
$d = new pic_data(); 
$d->data->urls[0] = 'avatar/'.$new_avatar_path;
$d->status = 1;
$d->statusText = '上传成功!';

$msg = json_encode($d);

echo $msg;

//log_result($msg);
function  log_result($word) {
	@$fp = fopen("log.txt","a");	
	@flock($fp, LOCK_EX) ;
	@fwrite($fp,$word."：执行日期：".strftime("%Y%m%d%H%I%S",time())."\r\n");
	@flock($fp, LOCK_UN); 
	@fclose($fp);
}
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