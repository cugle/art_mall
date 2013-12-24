<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$Amedia_type = array(1=>'图片',2=>'Flash',3=>'代码',4=>'文字');
$db_table    = $pre.'ad';
if($_POST['act'] == 'insert' || $_POST['act'] == 'update' )
{

	/*处理图片*/
	if($_FILES['ad_code']['size'] > 0 )
	{
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['ad_code']['type']))
        {
            $strMessage =  '文件类型错误';
			$ad_code = $_POST['old_ad_code'];
        }
		else
		{

	       if(!empty($_POST['old_ad_code']))
	       {
               $ad_code = basename($image->upload_image($_FILES['ad_code'],'abcde',$_POST['old_ad_code']));
	       }
	       else
	       {
		       $ad_code = basename($image->upload_image($_FILES['ad_code'],'abcde'));

	       }
		}
	}
	else
	{
		if($_POST['old_ad_code'])
		{
           $ad_code = $_POST['old_ad_code'];
		}
		
		if($_POST['ad_code2'])
		{
            $ad_code = $_POST['ad_code2'];
			$ad_code = str_replace('http://','', $ad_code);
		    $ad_code = str_replace('https://','',$ad_code);
			$ad_code = 'http://'.$ad_code;
             
		}

		if($_POST['ad_code'])
		{
           $ad_code = $_POST['ad_code'];
		}
	}
    

	$is_insert   = $_POST['act'] == 'insert';
    if(trim($_POST[ad_name]) == ''  || trim($_POST[media_type]) == '')
	{
		$strMessage = '广告名称、类型不能为空';
	}
	else
	{ 
	  $start_date = trim($_POST["start_date"]);
	  $end_date   = trim($_POST["end_date"]);
	  $start_time = local_strtotime($start_date);
	  $end_time   = local_strtotime($end_date);
	  $myuser_id   = $_SESSION['auser_id'];

	  if($is_insert)
	  {
	    /* 入库 */
		$sql = "INSERT INTO " . $db_table . " (ad_name,position_id,user_id,media_type,ad_link,ad_code,start_time,end_time,start_date,end_date,link_man,link_email,link_phone,enabled,`domain_id` )" .
                 "VALUES ('$_POST[ad_name]','$_POST[position_id]','$myuser_id', '$_POST[media_type]', '$_POST[ad_link]',  '$ad_code','$start_time','$end_time','$start_date','$end_date','$_POST[link_man]','$_POST[link_email]','$_POST[link_phone]','$_POST[enabled]','".$Aconf['domain_id']."')"; 
        $oPub->query($sql);
		$strMessage = '成功添加';

	  }
      else if($_POST['act'] == 'update' && $_POST['ad_id'] > 0)
	  {
		$ad_id = $_POST['ad_id']+0;
        $sql = "UPDATE " . $db_table . " SET 
               `ad_name`    ='$_POST[ad_name]' , 
			   `user_id`    ='$_POST[myuser_id]' , 
               `position_id`='$_POST[position_id]' , 
               `media_type` ='$_POST[media_type]' , 
               `ad_link`    ='$_POST[ad_link]',
			   `ad_code`    ='$ad_code',
			   `start_time` ='$start_time',
			   `end_time`   ='$end_time',
			   `start_date` ='$start_date',
			   `end_date`   ='$end_date',
			   `link_man`   ='$_POST[link_man]',
			   `link_email` ='$_POST[link_email]',
			   `link_phone` ='$_POST[link_phone]',
			   `enabled`    ='$_POST[enabled]' 
		        WHERE `ad_id` ='".$ad_id."'
				AND `domain_id` = '".$Aconf['domain_id']."'";
        $oPub->query($sql);
		$strMessage = '成功修改';
	  }
	}
} 

if($_REQUEST["ad_id"])
{ 
	$ad_id = $_REQUEST["ad_id"];
	$sql = "SELECT a.*,b.ad_width,b.ad_height FROM ".$db_table ." as a,".$pre ."ad_position as b 
			where a.ad_id = '".$ad_id."' and a.position_id = b.position_id";
    $work = $oPub->getRow($sql);
	//media_type 
}
//广告类型
$Stropt = '<SELECT NAME="media_type" onchange="chkSearch(this.options[this.options.selectedIndex].value)">';
$n = 0;
while( @list( $key, $value ) = @each($Amedia_type) ) {
	   $n ++;
	   if(!$_REQUEST["media_type"]) {
           $selected = ($work['media_type'] == $key)? 'SELECTED':'';
	   } else {
           $selected = ($_REQUEST["media_type"] == $key)? 'SELECTED':'';  
	   }
       $Stropt .= '<OPTION VALUE="'.$key.'" '.$selected.' >'.$value.'</OPTION>';
}
$Stropt .= '</SELECT>';
//依据广告类型显示不同的输入框
$strMedia_type = '';
$ad_link = ($work["ad_id"] > 0)?$work["ad_link"]:'';
$ad_code = ($work["ad_id"])?$work["ad_code"]:'';
if($_REQUEST["media_type"]) {
   $media_type = $_REQUEST["media_type"];
} else {
   $media_type = ($work['media_type'])?$work['media_type']:1;
}
switch ($media_type) {
  case 2:
    $strMedia_type ="<b>广告链接：</b>";
	$strMedia_type .=" <input name=\"ad_link\" type=\"text\"  value=\"".$ad_link."\"/>";
	$strMedia_type .="<br/>";
	$strMedia_type .="<span style=\"margin-left: -36px\"><b>上传Flash文件：</b></span>";
	$strMedia_type .="<input type=\"file\" name=\"ad_code\" /><br/>";
	$strMedia_type .="<span style=\"color:#8F8F8F\">上传该广告的Flash文件,或者你也可以指定一个远程的Flash文件</span>";
	$strMedia_type .=" <INPUT type=\"hidden\" name=\"old_ad_code\"  value=\"".$ad_code."\" />";
	$strMedia_type .="<br/>";
	$strMedia_type .="<span style=\"margin-left: -24px\"><b>或Flash网址：</b></span>";
	$strMedia_type .=" <input name=\"ad_code2\" type=\"text\"   value=\"\"/>";
	$strMedia_type .= $ad_code;

	$tmpstr  = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="'.$work["ad_width"].'" height="'.$work["ad_height"].'">';
	if(strstr($ad_code,'http://' )) {
		$ad_codetmp = $ad_code;
	} else { 
		$ad_codetmp = $Aconf['domain_url'].'data/abcde/'.$ad_code;
	}
	$tmpstr .= '<param name="movie" value="'.$ad_codetmp.'" />';

    $tmpstr .= '<param name="quality" value="high" />';
    $tmpstr .= '<embed src="'.$ad_codetmp.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$work["ad_width"].'" height="'.$work["ad_height"].'"></embed>';
    $tmpstr .= '</object>';

    break;
  case 3:
	$strMedia_type ="<span style=\"margin-left: -26px\"><b>输入广告代码：</b></span>";
	$strMedia_type .=" <TEXTAREA NAME=\"ad_code\" style=\"width:600px;height:200px \">".$ad_code."</TEXTAREA><br/>";

	$tmpstr = '';
    break;
  case 4:
    $strMedia_type ="<b>广告链接：</b>";
	$strMedia_type .=" <input name=\"ad_link\" type=\"text\"   value=\"".$ad_link."\"/>";
	$strMedia_type .="<br/>";
	$strMedia_type .="<b>广告内容：</b>";
	$strMedia_type .=" <TEXTAREA NAME=\"ad_code\" ROWS=\"5\" COLS=\"30\">".$ad_code."</TEXTAREA><br/>";

	$tmpstr = '';
    break;
  default:
    $strMedia_type ="<b>广告链接：</b>";
	$strMedia_type .=" <input name=\"ad_link\" type=\"text\"  value=\"".$ad_link."\"/>";
	$strMedia_type .="<br/>";
	$strMedia_type .="<span style=\"margin-left: -26px\"><b>上传广告图片：</b></span>";
	$strMedia_type .=" <input type=\"file\" name=\"ad_code\"  size=\"30\"/><br/>";
	$strMedia_type .="<span style=\"color:#8F8F8F\">上传该广告的图片文件,或者你也可以指定一个远程URL地址为广告的图片</span>";
	$strMedia_type .=" <INPUT type=\"hidden\" name=\"old_ad_code\"  value=\"".$ad_code."\" />";
	$strMedia_type .="<br/>";
	$strMedia_type .="<span style=\"margin-left: -14px\"><b>或图片网址：</b></span>";
	$strMedia_type .=" <input name=\"ad_code2\" type=\"text\"   value=\"\"/>";
	$strMedia_type .= $ad_code;
	if(!empty($ad_code)){
		$strMedia_type .='<br/><IMG SRC="'.$Aconf['domain_url'].'data/abcde/'.$ad_code.'" width="'.$work["ad_width"].'" height="'.$work["ad_height"].'" BORDER="0" TITLE="'.$ad_link.'">';
	}

	$tmpstr =  '<A HREF="'.$Aconf['domain_url'].'ad_affiche.php?id='.$work["ad_id"].'" target="_blank"><IMG SRC="'.$Aconf['domain_url'].'data/abcde/'.$ad_code.'" WIDTH="'.$work["ad_width"].'" HEIGHT="'.$work["ad_height"].'" BORDER="0" TITLE="'.$ad_link.'"></A>';

    break;
}

//广告位置
$db_table = $pre."ad_position";
$sql = "SELECT position_id,position_name,type,ad_width,ad_height  FROM ".$db_table." order by position_id  asc";
$row = $oPub->select($sql);
$Strposopt = '<SELECT NAME="position_id">';
$tmp = ($work['position_id'] == 0)?'SELECTED':'';
$Strposopt .= '<OPTION VALUE="0" '.$tmp.'>站外广告</OPTION>';
if($row)
foreach ($row AS $key=>$val) {
	if($val[type] ) {
		if( $_SESSION['auser_name'] == 'admin') {
	        $selected = ($work['position_id'] == $val[position_id])? 'SELECTED':'';
	        $Strposopt .= '<OPTION VALUE="'.$val[position_id].'" '.$selected.' >系统广告位:'.$val[position_name].'['.$val[ad_width].'*'.$val[ad_height].']</OPTION>';
		}
	} else {
	   $selected = ($work['position_id'] == $val[position_id])? 'SELECTED':'';
	   $Strposopt .= '<OPTION VALUE="'.$val[position_id].'" '.$selected.' >'.$val[position_name].$val[position_name].'['.$val[ad_width].'*'.$val[ad_height].']</OPTION>';	
	}
}
$Strposopt .= '</SELECT>';

?>
<?php
include_once( "header.php");
if ($strMessage != '')
{
 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}

?>
<TABLE width="100%" border=0>

<form action="" method="post"  name="theForm"  enctype="multipart/form-data"  onsubmit="return validate();">
  <TR class=bg1>
    <TD align=left>
	    <p style="margin: 10px 0px 20px 100px">
         <b>媒介类型：</b>
		 <?php echo $Stropt;?>
         <br/>
		 <b>广告位置：</b>
		 <?php echo $Strposopt;?>
		 <br/>

         <b>广告名称：</b>
		 <input type="text" id="ad_name" name="ad_name" value="<?php echo ($work["ad_id"] > 0)?$work["ad_name"]:'';?>" size="20"/>
		 <br/><span style="color:#8F8F8F">广告名称只是作为辨别多个广告条目之用，并不显示在广告中</span>
		 <br/> 

		 <b>开始日期：</b>
		 <input name="start_date" type="text" size="10" value='<?php echo ($work["ad_id"] > 0)?$work["start_date"]:date("Y-m-d");?>' />
		 <br/>
		 <b>结束日期：</b>
		 <?php 
		    $tmp  = gmtime() + 7*24*60*60;
		 ?>
		 <input name="end_date" type="text"  size="10" value='<?php echo ($work["ad_id"] > 0)?$work["end_date"]:date("Y-m-d",$tmp);?>'/>

		 <div id="show_media_type" style="margin-left:100px;margin-top:-20px">
         <?php echo $strMedia_type;?>
		 </div>

		 <div style="margin-left:100px">
		 <b>是否开启：</b>
		 <INPUT TYPE="radio" NAME="enabled" value="1" <?php echo $work["enabled"]?'checked':'';?>/>是
		 <INPUT TYPE="radio" NAME="enabled" value="0" <?php echo !$work["enabled"]?'checked':'';?>>否
		 <br/>
		 <span style="margin-left: -14px"><b>广告联系人：</b></span>
		 <input name="link_man" type="text"  size="20" value='<?php echo ($work["ad_id"] > 0)?$work["link_man"]:'';?>'/>
		 <br/>
		 <span style="margin-left: -24px"><b>联系人Email：</b></span>
		 <input name="link_email" type="text"  size="20" value='<?php echo ($work["ad_id"] > 0)?$work["link_email"]:'';?>'/>
		 <br/>
		 <b>联系电话：</b>
		 <input name="link_phone" type="text"  size="20" value='<?php echo ($work["ad_id"] > 0)?$work["link_phone"]:'';?>'/>
         <br/><br/>
          <input type="submit" value="<?php echo ($work["ad_id"] > 0)?'修改广告':'提交新广告';?>" style="background-color: #FFCC66;margin-left:70px"/>
	      <input type="hidden" name="ad_id" value="<?php echo ($work["ad_id"] > 0)?$work["ad_id"]:0;?>" id="ad_id" />
          <input type="hidden" name="act" value="<?php echo ($work["ad_id"] > 0)?'update':'insert';?>" /> 
	      
		  </div>
		 </p>
		 <?php
		 if($work["ad_id"] > 0)
		 {
            $str ='<span style="color:#990000">广告统计返回地址:</span> ';
            $str .= $Aconf['domain_url'].'ad_affiche.php?id='.$work["ad_id"].'<br/>复制广告代码:<br/>';
			if($tmpstr) {
				$str .='<TEXTAREA  style="width: 600px;height: 100px">'.$tmpstr.'</TEXTAREA>'; 
			 } 
			echo $str;
		 }
		 ?>
         
	</TD> 
  </TR>
  </form>
 </table>
<script type="text/javascript" language="JavaScript">

function chkSearch(obj)
{  
   location="<?php echo $_SERVER["PHP_SELF"];?>?media_type=" + obj + "&ad_id=" + "<?php echo $work["ad_id"];?>"; 
}

function validate() { 
	var ad_name = document.getElementById("ad_name").value;  

	var msg = '';
	var reg = null;

	if( ad_name.length < 1 ){
		msg += '广告名称不能为空' + '\n';
	} 

	if (msg.length > 0){
		alert(msg);
		return false;
	}else
	{
		return true;
	}
}
</script>
<?php
include_once( "footer.php");
?>

