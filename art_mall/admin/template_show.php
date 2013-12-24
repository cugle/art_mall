<?php
//producttxt.states = 0/1/2 正常/删除/已销售
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");
//数据来源列表
$StroptAitname = '<SELECT NAME="template_name" onchange="template_desc(this.value)">';
foreach( $Aitname as $k => $v ) 
{ 
    $n ++;
    //$selected = ($work['aaid'] == $value["aaid"])? 'SELECTED':'';
    $StroptAitname .= '<OPTION VALUE="'.$k.'">'.$v.'</OPTION>';
}
$StroptAitname .= '</SELECT>';
if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}
?>
<!DOCTYPE HTML>
<html lang="en"> 
<head>
	<meta charset="utf-8" />
	<title>行业之星页面设置</title>
	<meta name="description" content="" />
	<meta name="keywords" content="" /> 
	<link href="template_css/style.css" rel="stylesheet" type="text/css" />
	<!--[if lt IE 9]>
		<script src="template_js/html5.js"></script>
	<![endif]--> 
</head> 
<body> 
<div id="wrapper">
	<header>
		<div class="txt">当前设置页面:首页</div>
 
		<div  id=template_show style="display:none"> 
			数据来源:<?php echo $StroptAitname;?>
			<br/>
			模块名称:<INPUT TYPE="text" NAME="t_name" style="width:80px;"> 
			宽:<INPUT TYPE="text" NAME="t_width" style="width:40px;">PX  
			高:<INPUT TYPE="text" NAME="t_" style="width:40px;">PX  
		</div>
		<div class="line"></div>
		<div id="template_descs" style="display:none" >
			<?php echo $Aconf['descs'];?>
		</div>
		<div class="line"></div>
		<b>自定义,设置方法：</b><br/>
		1.用鼠标拖拽，自定义“模块显示位置”<br/>
		2.点击“编辑模块内容”能修改“模块标题、显示内容等资料”。
	</header>

	<section id="container"> 
		<ul id="demo">
			<li style="width:425px;height:320px;"> 
				<div style="width:425px;height: 300px;background-color: #009900"> 
				</div>
				<span style="float:left">1</span> 
				<div style="float:right"><a onmousedown="return template(1)">编辑模块内容</a></div>
			</li>
			<li>
				<img src="template_img/zahia01.jpg" alt="zahia01" width="200" height="300" />
				<span>2</span>
				<div style="float:right"><a onmousedown="return template(2)">编辑模块内容</a></div>
			</li> 
			<li><img src="template_img/zahia03.jpg" alt="zahia03"
			width="200" height="300" />
			<span>3</span></li>
			<li><img src="template_img/zahia04.jpg" alt="zahia04"
			width="200" height="300" />
			<span>4</span></li>
			<li><img src="template_img/zahia05.jpg" alt="zahia05"
			width="200" height="300" />
			<span>5</span></li>
			<li><img src="template_img/zahia06.jpg" alt="zahia06"
			width="200" height="300" />
			<span>6</span></li> 
			<li><img src="template_img/zahia07.jpg" alt="zahia07"
			width="200" height="300" />
			<span>7</span></li> 
			<li><img src="template_img/zahia08.jpg" alt="zahia08"
			width="200" height="300" />
			<span>8</span></li> 
			<li><img src="template_img/zahia09.jpg" alt="zahia09"
			width="200" height="300" />
			<span>9</span></li> 
		</ul> 
	</section> 
</div>	 
<footer>        
	 <?php echo $Aconf['footer_title'];?> 
</footer>

	<script src="template_js/jquery-1.6.2.min.js" type="text/javascript"></script>
	<script src="template_js/jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script>
	<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
	<script type="text/javascript">
	$(function() {
		$('#demo').sortable({
			
			start: function(event, ui) {
				ui.item.addClass('active');
			},
			stop: function(event, ui) {
				ui.item.removeClass('active').effect(
					'highlight', 
					{ color : '#000' }, 1000, function() {
					$.each($('#demo li'), function(index, event) {
						$(this).children('span').html(parseInt(index, 10)+1);
					});
				});
			}
			
		});
		$('#demo').disableSelection();
		
	});

	function template(a)
	{
		 obj = "template_show";
		 document.getElementById(obj).style.display   ='block';
		 //var strTemp = "ajax_template.php?op=show" + "&edit_val=" + escape(a); 
		 //send_request(strTemp);
	}
	function template_desc(a)
	{
		 obj = "template_descs";
		 document.getElementById(obj).style.display   ='block'; 
	}
	</script>
 



</script>
</body>
</html>