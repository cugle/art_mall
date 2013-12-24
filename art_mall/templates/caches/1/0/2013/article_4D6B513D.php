<?php exit;?>a:3:{s:8:"template";a:4:{i:0;s:83:"/nfs/c04/h06/mnt/61335/domains/art.design-forward.com/html/themes/art//article.html";i:1;s:82:"/nfs/c04/h06/mnt/61335/domains/art.design-forward.com/html/themes/art//header.html";i:2;s:80:"/nfs/c04/h06/mnt/61335/domains/art.design-forward.com/html/themes/art//left.html";i:3;s:82:"/nfs/c04/h06/mnt/61335/domains/art.design-forward.com/html/themes/art//footer.html";}s:7:"expires";i:1378391724;s:8:"maketime";i:1378391724;}<div class="header">
  <div class="logo"><a href="/index.php"><img src="themes/art/images/logo.jpg" border="0" /></a></div>
  <div id="menu">
    <ul id="nav">
      
      <li  ><span class="note"> </span></li> 
       
	
																							  
														  
														<li class="mainlevel" id="mainlevel_01"><img src="/themes/art/images/menu2.jpg" width="105" height="56" />														<ul class="sub_nav_01">
    												    <span class="Triangle_con"></span>
																												 </ul>
	  </li>
												  
														  
														<li class="mainlevel" id="mainlevel_01"><a href="products.php?id=2"><img src="/themes/art/images/menu3.jpg" width="105" height="56" /></a>														<ul class="sub_nav_01">
    												    <span class="Triangle_con"></span>
																												 
															<li><a href="products.php?id=7">acrylic</a></li>
														 
															<li><a href="products.php?id=8">charcoal</a></li>
														 
															<li><a href="products.php?id=9">ink</a></li>
														 
															<li><a href="products.php?id=10">mix media</a></li>
														 
															<li><a href="products.php?id=11">pastel</a></li>
														 
															<li><a href="products.php?id=12">prints</a></li>
														 
															<li><a href="products.php?id=13">oil</a></li>
														 
															<li><a href="products.php?id=14">water color</a></li>
														 
																												 </ul>
	  </li>
												  
														  
														<li class="mainlevel" id="mainlevel_01"><img src="/themes/art/images/menu4.jpg" width="105" height="56" />														<ul class="sub_nav_01">
    												    <span class="Triangle_con"></span>
																												 </ul>
	  </li>
												  
														  
														<li class="mainlevel" id="mainlevel_01"><img src="/themes/art/images/menu5.jpg" width="105" height="56" />														<ul class="sub_nav_01">
    												    <span class="Triangle_con"></span>
																												 </ul>
	  </li>
												  
														  
														<li class="mainlevel" id="mainlevel_01"><img src="/themes/art/images/menu6.jpg" width="105" height="56" />														<ul class="sub_nav_01">
    												    <span class="Triangle_con"></span>
																												 </ul>
	  </li>
												  
														  
														<li class="mainlevel" id="mainlevel_01"><img src="/themes/art/images/menu1.jpg" width="105" height="56" />														<ul class="sub_nav_01">
    												    <span class="Triangle_con"></span>
																												 </ul>
	  </li>
												 
											
																						
	   
	   
    </ul>
  </div>
</div><DIV id=m_center></DIV>
<DIV id=m_right>
  <DIV id=p_title>
	<DIV class=p_left_nava>
		<ul>
			<li><A HREF="./">home</A> ></li><li><A HREF="articles.php">新闻列表</a> ></li><li><a href="articles.php?id=0"></a> ></li><li><span style="font-weight:lighter"> 正文</span></li>		</ul> 
	</DIV>
	<DIV class=p_right></DIV>
  </DIV>
  <DIV class=profile>
	<SPAN class=title>test</SPAN>
	<div class="titlesub">
	<A HREF="http://art.design-forward.com/">designart</A>   13年6月16日08:03 
	【<a href="javascript:SetFont(18)">大</a> <a href="javascript:SetFont(16)">中</a> <a href="javascript:SetFont(12)">小</a>】 
	 <A HREF="acomms.php?id=1" style="color:#FF6600">我要评论(554fcae493e564ee0dc75bdf2ebf94caexe|a:3:{s:4:"name";s:3:"exe";s:4:"char";s:13:"article_comms";s:4:"arid";s:1:"1";}554fcae493e564ee0dc75bdf2ebf94ca)  </A>
		
			<p style="background-color:#FEEFF5;padding:1px">
		<span style="color:#D02700;">编辑点评：</span>test for blog 
	</p>
		</div>  
	<div class="cont2" id="content_zhengwen"> 
		test					<span style="margin: 20px 20px 10px 400px"> [责任编辑：admin]</span> 
				  
	</div>
 
</DIV>
  <DIV class=line_b></DIV></DIV>
</DIV>
<script language="JavaScript">
 
	function article_vote(a,b) {
		obj = a;
		if(obj == 'DnumIn_comm' || obj == 'CnumIn_comm') {
			obj = a+"_"+b;
			var strTemp = "ajax_article_comms_vote.php?arcid=" + b + "&types=" + a;
		} else {
			var strTemp = "ajax_article_vote.php?arid=" + b + "&types=" + a;
		}
		send_request(strTemp); 
	}
	function validate() {
		var frm          = document.forms['theForm'];
		var descs = document.getElementById("my_descs").value; 
		var msg = '';
		if (descs.length == 0) {
			msg += '请输入正文' + '\n';
		}
		if (msg.length > 0) {
			alert(msg);
			return false;
		}else
		{
			return true;
		}
	}
	function copyLink() {
		var links = document.title  +location.href;
		window.clipboardData.setData("Text",links);
		alert("该网页地址已复制，您可以在QQ,MSN中粘贴(快截键CTRL+V)推荐给好友");
	}
	function copyURL() {
		var url = location.href;
		window.clipboardData.setData("Text",url);
		alert("该网页链接地址已复制，您可以粘贴(快截键CTRL+V)在需要的地方");
	}
	function SetFont(size){
		var divBody = document.getElementById("content_zhengwen");
		if(!divBody) {
		  return;
		}
		divBody.style.fontSize = size + "px";
		var divChildBody = divBody.childNodes;
		for(var i = 0; i < divChildBody.length; i++) {
		  if (divChildBody[i].nodeType==1) {
			  divChildBody[i].style.fontSize = size + "px";
		  }
		}
	} 
	function SetFont(size){
		var divBody = document.getElementById("content_zhengwen");
		if(!divBody) {
		  return;
		}
		divBody.style.fontSize = size + "px";
		var divChildBody = divBody.childNodes;
		for(var i = 0; i < divChildBody.length; i++) {
		  if (divChildBody[i].nodeType==1) {
			  divChildBody[i].style.fontSize = size + "px";
		  }
		}
	} 
	function loadvcode() {
		document.getElementById('span_img').innerHTML="<img src=\"http://art.design-forward.com/vcode.php\" alt=\"点击刷新验证码\" onclick=\"document.getElementById('vcode').src='http://art.design-forward.com/vcode.php?'+Math.random();\" style=\"cursor:pointer;width:65px;height:18px;\" id=\"vcode\" align=\"absmiddle\" />";
	}
 
</script>
<div id="ToolBar"><ul>
<li><a href="/artists.php"><img src="themes/art/images/toolbar1.jpg" border="0" /></a></li>
<li><a href="#"><img src="themes/art/images/toolbar2.jpg" border="0" /></a></li>
<li><a href="/about.php"><img src="themes/art/images/toolbar3.jpg" border="0" /></a></li>
<li><a href="/contactus.php"><img src="themes/art/images/toolbar4.jpg" border="0" /></a></li>
<li><a href="#"><img src="themes/art/images/toolbar5.jpg" border="0" /></a></li>
<li><a href="/user.php?o=r"><img src="themes/art/images/toolbar6.jpg" border="0" /></a></li>
<li><a href="/user.php?o=car"><img src="themes/art/images/toolbar7.jpg" border="0" /></a></li>
<li><a href="/user.php"><img src="themes/art/images/toolbar8.jpg" border="0" /></a></li>
<li><a href="#"><img src="themes/art/images/toolbar9.jpg" border="0" /></a></li>
<li><a href="#"><img src="themes/art/images/toolbar10.jpg" border="0" /></a></li>
  </ul></div>