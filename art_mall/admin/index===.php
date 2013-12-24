<?php
define('IN_OUN', true);
include_once( "./includes/admincommand.php");
?>
<HTML>
  <HEAD>
  <TITLE><?php echo $Aconf['header_title'];?> 后台管理</TITLE>
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<SCRIPT language="JavaScript" type="Text/Javascript">
	<!-- 
	if (window.top != window) {
	  window.top.location.href = document.location.href;
	} 
	//-->
	</SCRIPT>
  </HEAD>  <frameset rows="75,*,22" cols="*" frameborder="no" border="0" framespacing="0">	<frame src="top.php" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" />	<frameset id=frame-body name="MainFrame" cols="110,10, *" frameborder="no" border="0" framespacing="0" scrolling="yes" bordercolor="#A1C7F9">
	  <frame id=menu-frame name="left"  noresize="true"  src="nave.php" scrolling="no" noresize="true">
	  <frame id=drag-frame name="drag-frame" src="hidebar.html" scrolling="no" noresize="true">
	  <frame id=main-frame name="main" src="default.php">
	</frameset>	<frame src="foot.php" name="bottomFrame" scrolling="no" noresize="noresize" /></frameset><noframes><body style="padding:0"></body></noframes></html>