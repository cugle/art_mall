<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");  
include_once( ROOT_PATH."kindeditor/php/JSON.php");  
//图片扩展名
$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp'); 
//目录名 
$dir_name = empty($dir) ? 'image' : trim($dir);
if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
	echo "Invalid Directory name.";
	exit;
}
 
$root_path = ROOT_PATH.'images'; 
$current_path = $root_path;
$current_url = $root_url;
$current_dir_path = $root_path;
$moveup_dir_path = $root_path;

echo realpath($root_path);
//排序形式，name or size or type
$order ='name';
 
//遍历目录取得文件信息
$file_list = array();
$row = $oPub->select('SELECT fileid,filename FROM '.$pre.'arti_file WHERE  type="'.$jsonop.'" and domain_id="'.$Aconf['domain_id'].'" ORDER BY fileid DESC'); 
$i = 0;
while( @list( $key, $value ) = @each( $row) ) {

	$file = ROOT_PATH .$value['filename'];
	$file_list[$i]['is_dir'] = false;
	$file_list[$i]['has_file'] = false;
	$file_list[$i]['filesize'] = filesize($file);
	$file_list[$i]['dir_path'] = '';
	$file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
	$file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr); 
	$file_list[$i]['filetype'] = $file_ext; 

	$file_list[$i]['filename'] = $value['filename']; //文件名，包含扩展名
	$file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
	$i++;
}  

//排序
function cmp_func($a, $b) {
	global $order;
	if ($a['is_dir'] && !$b['is_dir']) {
		return -1;
	} else if (!$a['is_dir'] && $b['is_dir']) {
		return 1;
	} else {
		if ($order == 'size') {
			if ($a['filesize'] > $b['filesize']) {
				return 1;
			} else if ($a['filesize'] < $b['filesize']) {
				return -1;
			} else {
				return 0;
			}
		} else if ($order == 'type') {
			return strcmp($a['filetype'], $b['filetype']);
		} else {
			return strcmp($a['filename'], $b['filename']);
		}
	}
}
usort($file_list, 'cmp_func');

$result = array();
//相对于根目录的上一级目录
$result['moveup_dir_path'] = $moveup_dir_path;
//相对于根目录的当前目录
$result['current_dir_path'] = $current_dir_path;
//当前目录的URL
$result['current_url'] = $Aconf['domain_url'];
//文件数
$result['total_count'] = count($file_list);
//文件列表数组
$result['file_list'] = $file_list;

//输出JSON字符串
header('Content-type: application/json; charset=UTF-8');
$json = new Services_JSON();
echo $json->encode($result);
