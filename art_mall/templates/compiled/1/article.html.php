<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->_var['header_title']; ?>|<?php echo $this->_var['title']; ?></title> 
	<meta name="description" content="<?php echo $this->_var['description']; ?>">
	<meta name="keywords" content="<?php echo $this->_var['keywords']; ?>">  
<link href="<?php echo $this->_var['template_path']; ?>css/css.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $this->_var['template_path']; ?>css/menu.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo $this->_var['template_path']; ?>js/jquery-1.4.2.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $this->_var['template_path']; ?>js/menu.js"></script>
<style>

body{
	padding:0;
	height: 90px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0;
	margin-left: 0px;
}

img{border:0}

#container{text-align:center}

#container .cell{padding:5px 5px 0; border:1px solid #E3E3E3; background:#F5F5F5; margin-top:10px}

#container p{line-height:20px; margin-top:5px}
a:link {
	color: #000000;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #000000;
}
a:hover {
	text-decoration: none;
	color: #000000;
}
a:active {
	text-decoration: none;
	color: #000000;
}
#w900_2 {
	width: 880px;
	margin-right: auto;
	margin-left: auto;
}
</style>
 
</head>

<body>
<?php echo $this->fetch('header.html'); ?> 
<div class="main">
  
  
<div class="mainbody">
<div id="w900_2">
  <div class="right_nr">
		<div class="top"><ul><?php echo $this->_var['home']['nowNave']; ?></ul></div>
		<div class="nr">
<DIV class=profile>
	<SPAN class=title><?php echo $this->_var['home']['article']['name']; ?></SPAN>
	<div class="titlesub">
	<A HREF="<?php echo $this->_var['home']['article']['sourhttp']; ?>"><?php echo $this->_var['home']['article']['sour']; ?></A>   <?php echo $this->_var['home']['article']['arti_date']; ?> 
	【<a href="javascript:SetFont(18)">大</a> <a href="javascript:SetFont(16)">中</a> <a href="javascript:SetFont(12)">小</a>】 
	 <A HREF="<?php echo $this->_var['home']['article']['arcomms_url']; ?>" style="color:#FF6600">我要评论(<?php 
$k = array (
  'name' => 'exe',
  'char' => 'article_comms',
  'arid' => $this->_var['home']['article']['arid'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>)  </A>
	<?php if ($this->_var['home']['article']['ifpic']): ?><a href="<?php echo $this->_var['home']['article']['pic_url']; ?>" style="color:#f00">[图库方式]</a><?php endif; ?>
	
	<?php if ($this->_var['home']['article']['keys']): ?>
	<p class=pic> 
		 <b>搜索：</b>
		 <?php $_from = $this->_var['home']['article']['keys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'keys');if (count($_from)):
    foreach ($_from AS $this->_var['keys']):
?>
			 <A href="http://www.baidu.com/s?wd=<?php echo $this->_var['keys']['keys']; ?>&amp;tn=ddd50" 
	target="_blank"><?php echo $this->_var['keys']['keys']; ?></A>&nbsp;&nbsp; 
		 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 	
	</p>
	<?php endif; ?>
	<?php if ($this->_var['home']['article']['edit_comm']): ?>
	<p style="background-color:#FEEFF5;padding:1px">
		<span style="color:#D02700;">编辑点评：</span><?php echo $this->_var['home']['article']['edit_comm']; ?> 
	</p>
	<?php endif; ?>
	</div>  

	<div class="cont2" id="content_zhengwen"> 
		<?php echo $this->_var['home']['article']['descs']; ?>
		<?php if ($this->_var['home']['article']['user_id']): ?>
			<span style="margin: 20px 20px 10px 400px"> [责任编辑：<?php echo $this->_var['home']['article']['user_id']; ?>]</span> 
		<?php endif; ?>
		<?php if ($this->_var['home']['article']['articel_page']): ?>
		<div class="fenye"><?php echo $this->_var['home']['article']['articel_page']; ?></div> 
		<?php endif; ?>  
	</div>
 
</DIV>
		</div>
	</div> 
</div>
</div>
<div class="footer"></div>
</div>
<?php echo $this->fetch('footer.html'); ?> 
</body>
</html>
