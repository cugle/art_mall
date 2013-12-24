<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<LINK media=all href="css/style.css" type=text/css rel=stylesheet>
</head>
<BODY>
<div id="right">
  <!--右侧-->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr class="bg_header" >
      <td width="40%">
	   <?php echo $nowName;?>  
      </td>	 
	  <td>
	  <span style="float:right">
	  <?php 
		$str = '';
		if($haveMessages){
			$str = '<span style="color:#FF0000;">'.$straud.'</span>';
		}else{
			$str = '<span >站内短信</span>';
		}
		$str = ' <a href="messagesuser.php" target="main">'.$str.'</a> ';

		if($_SESSION['auser_name']) 
			echo $str.'<A HREF="../"  target="_brank">home page浏览首页</A> <A HREF="adminmy_base.php">change password密码修改</A> <A HREF="clear_all_files.php">refresh cached缓存刷新</A> <A HREF="logoff.php">logout退出</A>' ;
	  ?>
	  </span>
	  </td>
    </tr>
  </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="3" class="bg_blue"></td>
    </tr>
  </table>
