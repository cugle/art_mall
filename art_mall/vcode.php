<?php
define('IN_OUN', true); 
include_once( "./includes/command.php");
$width      =65;
$height     =20;
$font_size  =5;
$sessionval = '';
$sourcestrings="23456789QWERTYUPASDFGHJKLZXCVBNM";
$image=imagecreate($width,$height);
$colorarrs=array(
    imagecolorallocate($image,255,255,255),//white
    imagecolorallocate($image,140  ,240  ,120), //black 
	imagecolorallocate($image,126  ,45  , 120) //black 
);
unset($sessionval);
imageline($image,0,0,      $width-1,$height,$colorarrs[1]);  
imageline($image,0,$height,$width,3,$colorarrs[2]);  
imagesetthickness($image,3);
//随机得到字符串个数
$strsize=rand(3,4);
imagefill($image,0,0,$colorarrs[0]);
//一个个的写字符串到图片
for($i=0;$i<$strsize;$i++){
    $i_temp=rand(1,31);
    $sessionval .=$sourcestrings[$i_temp];
    $fontcolor=imagecolorallocate($image,rand(0,155),rand(0,155),rand(0,155));
    $y_i = $height/2 + $font_size /3 ;
    imagechar($image,$font_size, 1+ $i * $width /$strsize,5,$sourcestrings[$i_temp],$fontcolor);
}
//写入session,以后验证用
unset($_SESSION['vCode']);
$_SESSION['vCode'] = $sessionval;
ob_clean();
header('content-type:image/png');
imagepng($image);
imagedestroy($image); 

?>