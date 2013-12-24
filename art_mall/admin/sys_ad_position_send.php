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

$db_table = $pre.'ad_position';
if($_POST['act'] == 'insert' || $_POST['act'] == 'update' )
{
	$is_insert   = $_POST['act'] == 'insert';
    if(trim($_POST[position_name]) == '' || trim($_POST[ad_width]) == '' || trim($_POST[ad_height]) == '')
	{
		$strMessage = '广告位名称、宽度、高度都不能为空';
	}
	else
	{      
	  if($is_insert)
	  {
	    /* 入库 */		
		$sql = "INSERT INTO " . $db_table . " (position_name,ad_width,ad_height,position_desc,type, `domain_id` )" .
                 "VALUES ('$_POST[position_name]','$_POST[ad_width]', '$_POST[ad_height]', '$_POST[position_desc]',  '$_POST[type]','".$Aconf['domain_id']."')"; 
        $oPub->query($sql);
		$strMessage = '成功添加';

	  }
      else if($_POST['act'] == 'update' && $_POST['position_id'] > 0)
	  {
		$position_id = $_POST['position_id']+0;
        $sql = "UPDATE " . $db_table . " SET 
               `position_name`='$_POST[position_name]' , 
               `ad_width`='$_POST[ad_width]' , 
               `ad_height`='$_POST[ad_height]' , 
               `position_desc`='$_POST[position_desc]',
			   `type`='$_POST[type]' 
		        WHERE `position_id` ='".$position_id."'";
        $oPub->query($sql);
		$strMessage = '成功修改';
	  }
	}
}



if($_REQUEST['action'] == 'edit' && $_REQUEST[position_id])
{ 
	$position_id = $_REQUEST[position_id];
	$sql = "SELECT *     
	        FROM ".$db_table ."  
			where position_id = '".$position_id."'";
    $work = $oPub->getRow($sql);
}
?>
<?php
	include_once( "header.php"); 
	if ($strMessage != '')
	{
	 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
	}
?>


<TABLE width="100%" border=0>
 
<form action="" method="post" name="theForm" >
  <TR class=bg1>
    <TD align=left> 
 
	    <p style="margin: 20px">
         <b>广告位名称：</b>
		 <input type="text" name="position_name" value="<?php echo ($work["position_id"] > 0)?$work["position_name"]:'';?>" size="20"/>
		 <br/>
 
         <b>广告位类型：</b>
		 <input type="radio" name="type" value="0" <?php echo (!$work["type"])?'checked':'';?>/>用户广告位
		 <input type="radio" name="type" value="1" <?php echo ($work["type"])?'checked':'';?>/>系统广告位
		 [<span style="font-size: 12px;color:#c00">系统广告位:主站用户添加的广告为系统广告,用户广告位，将显示主广告</span>]
         <br/>
		 <b>广告位宽度：</b>
		 <input type="text" name="ad_width" size="20" value="<?php echo ($work["position_id"] > 0)?$work["ad_width"]:'';?>" />像素
		 <br/>
         <b>广告位高度：</b>
		 <input type="text" name="ad_height" size="20" value="<?php echo ($work["position_id"] > 0)?$work["ad_height"]:'';?>" />像素
         <br/>
		 <b>广告位描述：</b>
		 <input type="text" name="position_desc" size="50" value="<?php echo ($work["position_id"] > 0)?$work["position_desc"]:'';?>" />
		 <br/>
		 <?php
		 if($work["position_id"] > 0){
			 echo  '广告调模版调用方法 {ads_'.$work["ad_width"].'_'.$work["ad_height"].'_'.$work["position_id"].'}'; 
		 }
		 
		 ?>
       <input type="submit" value="<?php echo ($work["position_id"] > 0)?'修改广告位':'提交新广告位';?>" style="background-color: #FFCC66;margin-left:85px"/>
	   <input type="hidden" name="position_id" value="<?php echo ($work["position_id"] > 0)?$work["position_id"]:0;?>" id="position_id" />
       <input type="hidden" name="act" value="<?php echo ($work["position_id"] > 0)?'update':'insert';?>" /> 
		 </p>
         
	</TD> 
  </TR>
  </form>
 </table>
<?php
include_once( "footer.php");
?>

