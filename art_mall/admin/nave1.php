<?php 
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");
//查找用户权限导航 
if ( $_SESSION['aaction_list'] == 'all') {
	$strnave = '';
    foreach ($admin_nave AS $ak=>$av) {
		$strnave .= '<li><h6>&nbsp;'.$Aprive[$ak].'</h6><ul>'; 
		foreach ($av AS $v) {
            $strnave .= '<li>&nbsp;<A href="'.$v.'.php" target=main>'.$Aprive[$v].'</A></li>';
		}
        $strnave .= '</ul></li>';
	}
} else {
	$Atmp = explode(",",$_SESSION['aaction_list']);
	 if($Atmp) //重新组合显示顺序
     foreach ($Atmp AS $av) {
		foreach ($admin_nave AS $akx=>$akv)
		{
			if(in_array($av,$akv)){
				$admin_nave_tmp[$akx].=$av.',';
				break;
			}
		} 
	}//foreach ($Atmp AS $av)
    $strnave = '';
	if(is_array($admin_nave_tmp))
		foreach ($admin_nave_tmp AS $ak=>$av) {
			$strnave .= '<li><h6>&nbsp;'.$Aprive[$ak].'</h6><ul>';
			$av = explode(",",substr($av,0,-1));
			foreach ($av AS $v)
			{   
				$strnave .= '<li>&nbsp;<A href="'.$v.'.php" target=main>'.$Aprive[$v].'</A> </li>';
			}
			$strnave .= '</ul></li>';
		} 
}

?>
<HTML><HEAD><TITLE> </TITLE>
<META http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/nave.css" type="text/css" title="blue" media="screen, projection"/> 
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(function(){
  //accordions
  $('.nav_middle li ul').hide().filter(':first').show();
  $('h6').filter(':even:first').addClass('active');
    $('h6').click(function(){
	 if($(this).next().is(':visible')){
	   return $('.nav_middle li ul').slideUp().prev().removeClass('active');
	 }
	 if($(this).next().is(':hidden')){
	   $('.nav_middle li ul').slideUp().prev().removeClass('active');
	   $(this).addClass('active').next().slideDown();
	 }
	});
});
 </script>
</head>

<body class="nav_middle">
<div id="left">

  <table width="126" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="30" align="center" class="bg_blue"><div class="bg_blue" id="user"><?php echo '<a href="logoff.php" >'.$_SESSION['auser_name'].'退出</a>';?></div></td>
    </tr>
  </table>

 <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="126" height="30" align="center" valign="top" class="nav_middle">
	      <!--左侧导航-->
          <table width="126" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="30" align="center" class="nav_top"></td>
            </tr>
            <tr>
              <td height="500" align="left" valign="top">
               <div class="nav_middle"> <ul> <?php  echo $strnave; ?> </ul> </div>
			   <div class="line"></div>
			   <div class="power">
				PowerBy <A HREF="http://www.osunit.com" target="_blank" title="QQ:16953292">Osunit</A>
			   </div> 
              </td>
            </tr>
          </table>
		  <!--左侧导航结束-->
      </td>
    </tr>
  </table>
        
</div>
</body>
</html>
