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
<script type="text/javascript" src="/js/ajax.js"></script> 

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
<style>
	.user_box{width:450px;margin:20px 0 0 100px;clear:both;border-style:solid; border-width:1px; border-color:#CCCCCC;padding: 20px 0 20px 0;}
	.user{width:200px;float:left;font-weight: bold;text-align: right;}
	.user div{margin: 5px}
</style>
<div class="main">  
<div class="mainbody"> 
<div id="w900_2">
  <div class="right_nr">
		  <div class="top"><ul><?php echo $this->_var['home']['nowNave']; ?></ul></div>
				<div class="nr">
					<?php if ($this->_var['home']['strMessage']): ?> 
						<div style="color:#FF0000;font-size: 14px;width:500px;margin-left: 30px;"><?php echo $this->_var['home']['strMessage']; ?></div>
					<?php endif; ?>

					<?php if ($this->_var['home']['user_o'] == 'emailcheck'): ?>
					
						<ul>
						<?php echo $this->fetch('lib/users_emailcheck.html'); ?>
						</ul>
					<?php elseif ($this->_var['home']['user_o'] == 'f'): ?>
						
						<ul>
						<?php echo $this->fetch('lib/users_f.html'); ?>
						</ul>
					<?php elseif ($this->_var['home']['user_o'] == 'findpass'): ?>
						<ul>
						<?php echo $this->fetch('lib/users_findpass.html'); ?>
						</ul>
					<?php else: ?>
						 
						<?php if ($this->_var['user']['user_id'] > 0): ?>
							
							<?php if ($this->_var['home']['userstr']): ?>
								
								<ul>
								<?php echo $this->_var['home']['userstr']; ?>
								</ul>
							<?php else: ?>
								<?php if ($this->_var['home']['user_o'] == 'p'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_p.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'e'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_e.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'i'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_i.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'c'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_c.html'); ?>
									</ul> 
								<?php elseif ($this->_var['home']['user_o'] == 'addrs'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_addrs.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'car'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_car.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'ding'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_ding.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'new'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_new.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'cx'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_cx.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'sc'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_sc.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'special'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_special.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'qh'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_qh.html'); ?>
									</ul> 
								<?php elseif ($this->_var['home']['user_o'] == 'keep'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_keep.html'); ?>
									</ul>
								<?php elseif ($this->_var['home']['user_o'] == 'detail'): ?>
									<ul>
									<?php echo $this->fetch('lib/users_detail.html'); ?>
									</ul> 

								<?php else: ?> 
									<div style="width:620px;">
										<div style="width:440px;float:left;overflow:hidden;">
											<ul>
											<?php echo $this->fetch('lib/users_d.html'); ?>
											</ul>
										</div>
										<div style="float:left;text-align: center;width:170px;padding: 2px;overflow:hidden;">
											<?php if ($this->_var['home']['user']['avatar']): ?> 
												  <IMG SRC="<?php echo $this->_var['home']['user']['avatar']; ?>"  BORDER="0" title="<?php echo $this->_var['home']['user']['user_name']; ?>头像">  
											<?php endif; ?>
											<A HREF="user.php?o=i" style="color:#6633FF;font-weight: bold">添加修改头像</A>
										</div> 
									</div>
								<?php endif; ?>
								
							<?php endif; ?> 
	  
						<?php else: ?>
							<?php if ($this->_var['home']['user_o'] == 'r'): ?> 
								<ul class="user_box"> 
									<?php echo $this->fetch('lib/users_r.html'); ?> 	
								</ul>  

							<?php else: ?>
								<ul class="user_box"> 
									<?php echo $this->fetch('lib/users_l.html'); ?> 	
								</ul> 
							<?php endif; ?>
						<?php endif; ?> 
						
					<?php endif; ?>
					<div class="clear"></div> 
				</div>
		  </div>
	</div> 
</div>
<div class="footer"></div>
</div>
</div>
<?php echo $this->fetch('footer.html'); ?>  
</body>
</html>
