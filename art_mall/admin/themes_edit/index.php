<?php
if($_POST['mset'] > 0 && $_POST['row_1'] > 0 && $_POST['row_1'] > 0)
{
	$At_row['num']   = $_POST['mset'] + 0;
	$At_row['all_width'] = 1008;
	$At_row['row_1'] = $_POST['row_1'] + 0;
	$At_row['row_2'] = $_POST['row_2'] + 0;
	$At_row['row_3'] = $_POST['row_3'] + 0;
}

if($At_row['num'] < 1)
{
	$At_row['num']   = 3;
	$At_row['all_width'] = 1008;
	$At_row['row_1'] = 250;
	$At_row['row_2'] = 508;
	$At_row['row_3'] = 250;
} 
//$Aplate_h['content_dom0'] = 200; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>拖拽页面布局-行业之星测试版</title> 
	<meta name="description" content="{$description}">
	<meta name="keywords" content="{$keywords}">   
	<link href="move.css" rel="stylesheet" type="text/css"> 
	<script type="text/javascript" src="js/move.js"></script> 
</head> 
 
<body>
 
<div class="header">
	<div class="logo">
		<div class="txt">Logo</div>
		<div class="sub">1、系统属性设置-><A HREF="sysconfig.php">基本属性设置>></A></div>  
	</div>

	<div class="banner">
		<B style="color:#F00">自定义页面方法：</B>
		<br/>
		1.页面右上角 设置列数。定义列宽;
		<br/>
		2.在页面主体部分“鼠标拖拽”板块标题，可以对页面进行布局；
		<br/>
		3.点每个板块的"<span style="color:#F00">★</span>" 设置每个板块的调用内容。 
	</div>

	<div class="right">
		<form style="margin: 0px" ACTION="" name="theSelect" method="post"> 
			<div class="txt">
				<SELECT NAME="mset" id="mset" onchange="select_row(this.value)">
					<OPTION VALUE="0" SELECTED>设置主体页面列数</OPTION>
					<OPTION VALUE="2" <?php echo ($At_row['num'] == 2?'selected':'');?>>2列模式</OPTION>
					<OPTION VALUE="3" <?php echo ($At_row['num'] == 3?'selected':'');?>>3列模式</OPTION>
				</SELECT> 
			</div> 
			
			<div class="blank2"></div>
			<div class="txt">
				第一列宽度:<INPUT TYPE="text" id="row_1" NAME="row_1" value="<?php echo $At_row['row_1'];?>" style="width:30px;" onchange="set_row_1(this.value)">PX<br/>
				第二列宽度:<INPUT TYPE="text" id="row_2" NAME="row_2" value="<?php echo $At_row['row_2'];?>" style="width:30px;" onchange="set_row_2(this.value)">PX<br/> 
				<div id="html_row_3">
					第三列宽度:<INPUT TYPE="text" id="row_3" NAME="row_3" value="<?php echo $At_row['row_3'];?>" readonly style="width:30px;color:#BBB">PX 
				</div>
			</div>
			<div class="blank2"></div>
			<B> <INPUT TYPE="submit" value="主页宽度:1008PX 提交"></B> 
		</form>
	</div>
</div>

<div class="blank2"></div>

<div class=content>
	<div class=left id=dom0  style="width:<?php echo $At_row['row_1'];?>px;">
		<div class=mo id=m0> 
			<h1>
				<div class="txt" id="dom0_m0">dom0</div> 
				<div class="more" >more..</div>
			</h1>  
			<div class="nr" style="height:<?php echo $Aplate_h['content_dom0'] > 0?$Aplate_h['content_dom0']:120;?>px;">
				<div class="set" onMouseDown="controlDiv('content_dom0')">★</div>  
				<ul>
					<li>aaaaaaaaaaaaaaaa1</li>
					<li>aaaaaaaaaaaaaaaa2</li>
					<li>aaaaaaaaaaaaaaaa3</li>
					<li>aaaaaaaaaaaaaaaa4</li>
					<li>aaaaaaaaaaaaaaaa5</li>
					<li>aaaaaaaaaaaaaaaa6</li> 
				</ul>  
			</div>
		</div>

		<div class=mo id=m1> 
			<h1>
				<div class="txt" id="dom0_m1">dom1</div> 
				<div class="more" >more..</div>
			</h1>  
			<div class="nr" style="height:<?php echo $Aplate_h['content_dom1'] > 0?$Aplate_h['content_dom1']:120;?>px;">
				<div class="set" onMouseDown="controlDiv('content_dom1')">★</div>  
				<ul>
					<li>bbbbbbbbbb1</li>
					<li>bbbbbbb2</li>
					<li>bbbbbbbbbbbbbbbbbbb3</li>
					<li>bbbbbbbbbbbbbbbbb4</li>
					<li>bbbbbbbbbbbbbbbbb5</li>
					<li>bbbbbbbbbbbbbb6</li> 
				</ul>  
			</div>
		</div>

		<div class=mo id=m2> 
			<h1>
				<div class="txt" id="dom0_m1">dom2</div> 
				<div class="more" >more..</div>
			</h1>  
			<div class="nr" style="height:<?php echo $Aplate_h['content_dom2'] > 0?$Aplate_h['content_dom2']:120;?>px;">
				<div class="set" onMouseDown="controlDiv('content_dom2')">★</div>  
				<ul>
					<li>bbbbbbbasdfbbb1</li>
					<li>bbbffffbbbb2</li>
					<li>bbbbfwewefbbbbbbb3</li>
					<li>bbbbfwefwefbbbbb4</li>
					<li>bbtgergbbbbbbb5</li>
					<li>bbbbggwrgwbbbbb6</li> 
				</ul>  
			</div>
		</div>

		<div class=mo id=m3> 
			<h1>
				<div class="txt" id="dom0_m3">dom3</div> 
				<div class="more" >more..</div>
			</h1>  
			<div class="nr" style="height:<?php echo $Aplate_h['content_dom3'] > 0?$Aplate_h['content_dom3']:120;?>px;">
				<div class="set" onMouseDown="controlDiv('content_dom3')">★</div>  
				<ul>
					<li>bbdsfgbbbbbbbb1</li>
					<li>bfgfgb2</li>
					<li>bbbbFASGERGbbbbbb3</li>
					<li>bbGREERbbbbbbb4</li>
					<li>bbbbbbbSDFASFbbbbbb5</li>
					<li>bbbbbFWWEFbbbbbb6</li> 
				</ul>  
			</div>
		</div>
	</div>

	<div class=center id=dom1 style="width:<?php echo $At_row['row_2'];?>px;">
		<div class=mo id=m4>
			<h1>
				<div class="txt" id="dom1_m4">dom4</div> 
				<div class="more" >more..</div>
			</h1>  
			<div class="nr" style="height:<?php echo $Aplate_h['content_dom4'] > 0?$Aplate_h['content_dom4']:120;?>px;">
				<div class="set" onMouseDown="controlDiv('content_dom4')">★</div>  
				<ul>
					<li>bbdKYUKbbbbbb1</li>
					<li>bfKTYJTJ2</li>
					<li>HHTHbFTHRTHbbbbbb3</li>
					<li>bbHRRbbbbbbb4</li>
					<li>bbbbHRTHFASFbbbbbb5</li>
					<li>bbbbbFHRTHbbbb6</li> 
				</ul>  
			</div>
		</div>

		<div class=mo id=m5>
			<h1>
				<div class="txt" id="dom1_m5">dom5</div> 
				<div class="more" >more..</div>
			</h1>  
			<div class="nr" style="height:<?php echo $Aplate_h['content_dom5'] > 0?$Aplate_h['content_dom5']:120;?>px;">
				<div class="set" onMouseDown="controlDiv('content_dom5')">★</div>  
				<ul>
					<li>bVEWbbbbb1</li>
					<li>bfKVEWTJ2</li>
					<li>HHTHSTVSDHRTHbbbbbb3</li>
					<li>bbHRRbbbbbbb4</li>
					<li>bbbbHVSDVHFASFbbbbbb5</li>
					<li>bbbbbFHVWVWHbbbb6</li> 
				</ul>  
			</div>
		</div>

		<div class=mo id=m6>
			<h1>
				<div class="txt" id="dom1_m6">dom6</div> 
				<div class="more" >more..</div>
			</h1>  
			<div class="nr" style="height:<?php echo $Aplate_h['content_dom6'] > 0?$Aplate_h['content_dom6']:120;?>px;">
				<div class="set" onMouseDown="controlDiv('content_dom6')">★</div>  
				<ul>
					<li>bVEWfwfb1</li>
					<li>bfKgdfgergJ2</li>
					<li>HHTdfgfgVSDHRTHbbbbbb3</li>
					<li>bbHRfgbbbbbb4</li>
					<li>bbbbHafdVHFASFbbbbbb5</li>
					<li>bbbdfgVWVWHbbbb6</li> 
				</ul>  
			</div>
		</div>

		<div class=mo id=m7>
			<h1>
				<div class="txt" id="dom1_m7">dom7</div> 
				<div class="more" >more..</div>
			</h1>  
			<div class="nr" style="height:<?php echo $Aplate_h['content_dom7'] > 0?$Aplate_h['content_dom7']:120;?>px;">
				<div class="set" onMouseDown="controlDiv('content_dom7')">★</div>  
				<ul>
					<li>bVEtrhethWbbbbb1</li>
					<li>bfKVrgrgWTJ2</li>
					<li>HHTHSgerRTHbbbbbb3</li>
					<li>bbHRgrbbbb4</li>
					<li>bbbbHgeHFASFbbbbbb5</li>
					<li>bbbbbFgegregWHbbbb6</li> 
				</ul>  
			</div>
		</div>

	</div>

	<?php if($At_row['num'] == 3){ ?>
		<div class=right id=dom2 style="width:<?php echo $At_row['row_3'];?>px;">
			<div class=mo id=m8>
				<h1>
					<div class="txt" id="dom2_m8">dom8</div> 
					<div class="more" >more..</div>
				</h1>  
				<div class="nr" style="height:<?php echo $Aplate_h['content_dom8'] > 0?$Aplate_h['content_dom8']:120;?>px;">
					<div class="set" onMouseDown="controlDiv('content_dom8')">★</div>  
					<ul>
						<li>bVEtrhethWbbbbb1</li>
						<li>bfKVrgrgWTJ2</li>
						<li>HHTHSgerRTHbbbbbb3</li>
						<li>bbHRgrbbbb4</li>
						<li>bbbbHgeHFASFbbbbbb5</li>
						<li>bbbbbFgegregWHbbbb6</li> 
					</ul>  
				</div>
			</div>

			<div class=mo id=m9>
				<h1>
					<div class="txt" id="dom2_m9">dom9</div> 
					<div class="more" >more..</div>
				</h1>  
				<div class="nr" style="height:<?php echo $Aplate_h['content_dom9'] > 0?$Aplate_h['content_dom9']:120;?>px;">
					<div class="set" onMouseDown="controlDiv('content_dom9')">★</div>  
					<ul>
						<li>bVEtgsfhethWbbbbb1</li>
						<li>bfsdfdsfrgrgWTJ2</li>
						<li>HHTsdfrRTHbbbbbb3</li>
						<li>bbHsdfbbbb4</li>
						<li>bbbbsdfbbbbb5</li>
						<li>bbdffsdregWHbbbb6</li> 
					</ul>  
				</div>
			</div>

			<div class=mo id=m10>
				<h1>
					<div class="txt" id="dom2_m10">dom10</div> 
					<div class="more" >more..</div>
				</h1>  
				<div class="nr" style="height:<?php echo $Aplate_h['content_dom10'] > 0?$Aplate_h['content_dom10']:120;?>px;">
					<div class="set" onMouseDown="controlDiv('content_dom10')">★</div>  
					<ul>
						<li>bVEt啊是飞往俄Wbbbbb1</li>
						<li>bfsdf是的风格豆腐干gWTJ2</li>
						<li>HHTsdfasdfHbbbbbb3</li>
						<li>bbHsdfbbbb4</li>
						<li>bbbbsdfbbbbb5</li>
						<li>bbdffsdregWHbbbb6</li> 
					</ul>  
				</div>
			</div>

			<div class=mo id=m11>
				<h1>
					<div class="txt" id="dom2_m11">dom11</div> 
					<div class="more" >more..</div>
				</h1>  
				<div class="nr" style="height:<?php echo $Aplate_h['content_dom11'] > 0?$Aplate_h['content_dom11']:120;?>px;">
					<div class="set" onMouseDown="controlDiv('content_dom11')">★</div>  
					<ul>
						<li>bVEt啊是飞往俄Wbbbbb1</li>
						<li>bfsdf是的风格豆腐干gWTJ2</li>
						<li>HHTsdfasdfHbbbbbb3</li>
						<li>bbHsdfbbbb4</li>
						<li>bbbbsdfbbbbb5</li>
						<li>bbdffsdregWHbbbb6</li> 
					</ul>  
				</div>
			</div>
		</div> 
	<?php } ?>
</div>

<div class="blank2"></div>
 
<div class="footer">

		<A HREF="../admin/">网站管理 </A>  Power by <a href="http://www.osunit.com/about.php">行业之星</a> 
		<a href="http://www.miibeian.gov.cn/" target="_blank">备案编号：######## </a> 

</div>

<!--  弹出的设置层 start -->
<style type="text/css">
<!-- 
	.mydiv {
	/*background-color: #FFCC66;*/ 
	z-index:999;
	width: 640px;
	height: 250px;
	left:50%;
	top:50%;
	margin-left:-250px!important;/*FF IE7 该值为本身宽的一半 */
	margin-top:-60px!important;/*FF IE7 该值为本身高的一半*/
	margin-top:0px;
	position:fixed!important;/* FF IE7*/
	position:absolute;/*IE6*/
	_top:       expression(eval(document.compatMode &&
				document.compatMode=='CSS1Compat') ?
				documentElement.scrollTop + (document.documentElement.clientHeight-this.offsetHeight)/2 :/*IE6*/
				document.body.scrollTop + (document.body.clientHeight - this.clientHeight)/2);/*IE5 IE5.5*/

	}
	
	.bg,.popIframe {
	background-color: #666; display:none;
	width: 100%;
	height: 100%;
	_width: 0;
	_height: 0;
	left:0;
	top:0;/*FF IE7*/
	filter:alpha(opacity=50);/*IE*/
	opacity:0.5;/*FF*/
	z-index:1;
	position:fixed!important;/*FF IE7*/
	position:absolute;/*IE6*/
	_top:       expression(eval(document.compatMode &&
				document.compatMode=='CSS1Compat') ?
				documentElement.scrollTop + (document.documentElement.clientHeight-this.offsetHeight)/2 :/*IE6*/
				document.body.scrollTop + (document.body.clientHeight - this.clientHeight)/2);
	}
	.popIframe {
	filter:alpha(opacity=0);/*IE*/
	opacity:0;/*FF*/
	}
-->
</style> 
<div id="popDiv" class="mydiv" style="display:none;">  
	<div class="yh_tc_con">
	 <div class="yh_tc_titcon">
	  <span class="right_btncon_close"><a class="hz_btn_close" title="关闭"  onclick="closeDiv()"></a></span>
	  <span class="left_txtcon">板块内容编辑</span>
	 </div>
	 <div class="yh_tc_tebcon">
		<form style="margin: 0px" ACTION="#" target="_self" id="theForm" name="theForm" method="post" onsubmit="return checksub()"> 
		  <div class="blank10px"></div> 

			<div class="tabbox">
				<dl class="input_dl">
					<dt class="color333"><span class="colorF00 fdleft">*</span>标签名：</dt>
					<dd>
						<input id="t_name" class="mem_text_in" type="text" value="" maxlength="15" size="15" name="t_name"> 
					</dd>
				</dl> 
			</div> 
			  <div class="tabbox">
			   <dl class="input_dl"> 
				<dt class="color333"><span class="colorF00 fdleft">*</span>更多连接：</dt>
				<dd>
				 <INPUT TYPE="radio" NAME="t_more"  value="2">有 <INPUT TYPE="radio" NAME="t_more" value="1" checked>无
				</dd>
			   </dl>
			  </div>
		  <div class="tabbox">
		   <dl class="input_dl">
			<dt class="color333"><span class="colorF00 fdleft">*</span>调用内容：</dt>
			<dd>
				<SELECT NAME="">
					<OPTION VALUE="" SELECTED>选择调用的内容</OPTION>
					<OPTION VALUE="">新闻分类</OPTION>
					<OPTION VALUE="">商品分类</OPTION>
					<OPTION VALUE="">FLASH推广</OPTION>
					<OPTION VALUE="">自定义编辑内容</OPTION>
				</SELECT> 
			</dd>
		   </dl>
		  </div>
 
		  <div class="tabbox">
		   <dl class="input_dl">
			<dt class="color333"><span class="colorF00 fdleft">*</span>显示数量：</dt>
			<dd>
			 <input id="t_num" style="width:20px" type="text" value="1"  maxlength="2" size="25" name="t_num"> 
			</dd> 
		   </dl>
		  </div>

		  <div class="tabbox">
		   <dl class="input_dl">
			<dt class="color333"><span class="colorF00 fdleft">*</span>板块高度：</dt>
			<dd>
			 <input id="t_h" style="width:30px" type="text" value="120"  maxlength="2" size="25" name="t_h">PX
			</dd> 
		   </dl>
		  </div>
		  <div class="tabbox">
		   <dl class="input_dl">
			<dt class="color333"><span class="colorF00 fdleft">*</span>背景颜色：</dt>
			<dd>
			 <input id="t_color" style="width:30px" type="text" value=""  maxlength="2" size="25" name="t_color">
			</dd> 
		   </dl>
		  </div>
		  <div class="tabbox">
		   <dl class="input_dl">
			<dt class="color333"><span class="colorF00 fdleft">*</span>显示方式：</dt>
			<dd>
			  <SELECT NAME="t_type">
				<OPTION VALUE="1" SELECTED>列表方式</OPTION>
				<OPTION VALUE="2" >图文混排</OPTION>
				<OPTION VALUE="3" >图片滚动</OPTION>
				<OPTION VALUE="4">FLASH展播</OPTION>
			  </SELECT>
			</dd>
		   </dl>
		  </div>

		  <div class="tabbox">
		   <dl class="input_dl">
			<dt class="color333"><span class="colorF00 fdleft">*</span>滚动方式：</dt>
			<dd>
			  <SELECT NAME="t_type">
				<OPTION VALUE="1" SELECTED>不滚动</OPTION>
				<OPTION VALUE="2" >向上滚动</OPTION>
				<OPTION VALUE="3" >向左滚动</OPTION> 
			  </SELECT>
			</dd>
		   </dl>
		  </div>

		  <!--按钮-->
		  <div class="tabbox">
		   <dl class="input_dl">
			<dt></dt>
			<dd>
				<div class="blank10px"></div>
				<input id="" class="yh_tc_btn_qr" type="submit" name="" value="" tabindex=""></button>  
				<input id="t_id"               type="hidden" name="t_id"               value=""> 
				<INPUT TYPE="hidden" NAME="act" value="install">
				<div class="blank10px"></div>
			</dd>
		   </dl>
		  </div>
		 </form>
	 </div>
	 <div class="yh_tc_bymcon"></div>
	</div>
	
</div>

<iframe id='popIframe' class='popIframe' frameborder='0' ></iframe>


<script language="JavaScript"> 

	function controlDiv(a){ 
		document.getElementById('popDiv').style.display='block';
		document.getElementById('popIframe').style.display='block';
		document.getElementById('bg').style.display='block';   

		document.getElementById("t_id").value = a;
	}

	function closeDiv(){
		self.location.href=self.location.href;
		//document.getElementById('popDiv').style.display='none';
		//document.getElementById('bg').style.display='none';
		//document.getElementById('popIframe').style.display='none';  
	}

 

	function select_row(a){

		if(a == 2)
		{
			document.getElementById("row_1").value = 650;
			document.getElementById("row_2").value = 358; 
			document.getElementById("html_row_3").style.display = 'none'; 
		}else if(a == 3)
		{ 
			document.getElementById("row_1").value = 250;
			document.getElementById("row_2").value = 508;
			document.getElementById("row_3").value = 250;
			document.getElementById("html_row_3").style.display = 'block';
		}
		//var user_name = document.getElementById("user_name").value;  
	}

	function set_row_1(a)
	{
		var num = document.getElementById("mset").value;
		var row_1 = document.getElementById("row_1").value;
		var row_2 = document.getElementById("row_2").value;
		var row_3 = '';
		if(num == 2)
		{
			row_2 = 1008 - row_1;
			if(row_2 > 0)
			{
				document.getElementById("row_2").value = row_2; 
				document.getElementById("html_row_3").style.display = 'none'; 
			}else
			{
				alert("输入错误，超过允许范围");
			}
		}else if(num == 3)
		{
			row_3 = 1008 - row_1 - row_2;
			if(row_3 > 0)
			{
				document.getElementById("row_3").value = row_3; 
			}else
			{
				alert("输入错误，超过允许范围");
			}
		}
	}

	function set_row_2(a)
	{
		var num = document.getElementById("mset").value;
		var row_1 = document.getElementById("row_1").value;
		var row_2 = document.getElementById("row_2").value;
		var row_3 = '';
		if(num == 2)
		{
			row_1 = 1008 - row_2;
			if(row_1 > 0)
			{
				document.getElementById("row_1").value = row_1; 
				document.getElementById("html_row_3").style.display = 'none';
			}else
			{
				alert("输入错误，超过允许范围");
			}
		}else if(num == 3)
		{
			row_3 = 1008 - row_1 - row_2;
			if(row_3 > 0)
			{
				document.getElementById("row_3").value = row_3; 
			}else
			{
				alert("输入错误，超过允许范围");
			}
		}
	}

</script> 
<!--  弹出的设置层 end -->
</body>
</html>