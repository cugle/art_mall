								<li style="margin:1px;width:380px;clear:left">
									<div class="user">帐号：</div> <div style="width:150px;float:left"><?php echo $this->_var['home']['user']['user_name']; ?></div>
								</li> 
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">账户余额：</div> <div style="width:150px;float:left;color:#F00;font-size:14px"><?php echo $this->_var['home']['user']['money']; ?>元</div>
									<div> <A HREF="user.php?o=detail" style="color:#00C">转账汇款</A> </div>
								</li>
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">EMAIL：</div> <div style="width:150px;float:left"><?php echo $this->_var['home']['user']['email']; ?></div>
									<div><?php if ($this->_var['home']['user']['estats'] > 0): ?> <span style="color:#C00">邮箱已验证</span> <?php endif; ?></div>
								</li> 
								<?php if ($this->_var['home']['user']['estats'] < 1): ?>
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">EMAIL验证：</div>
									<div id="smptemail"><A style="cursor:pointer;color:#3333CC" onmousedown="email_check(<?php echo $this->_var['home']['user']['id']; ?>)">还没验证邮箱，点击后验证。</A></div> 
								</li> 
								<?php endif; ?>
								<li style="margin:5px;width:420px;clear:left;">
									<div class="user">登录时间：</div> <div style="width:150px;float:left"><?php echo $this->_var['home']['user']['last_time']; ?></div>
								</li> 
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">登录IP：</div> <div style="width:150px;float:left"><?php echo $this->_var['home']['user']['last_ip']; ?></div>
								</li> 
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">登录次数：</div> <div style="width:150px;float:left"><?php echo $this->_var['home']['user']['visit_count']; ?></div>
								</li> 
								<li style="margin:5px;width:420px;clear:left;color:#FF6600">
									<div class="user">上一次登录：</div> <div style="width:150px;float:left"><?php echo $this->_var['home']['user']['last_login']; ?></div>
								</li>  


								<li style="margin:5px;width:420px;clear:left">
									<div class="user">性别：</div> 
									<div style="width:150px;float:left"> <?php if ($this->_var['home']['user']['sex'] < 1): ?>男<?php endif; ?>  <?php if ($this->_var['home']['user']['sex'] == 1): ?>女<?php endif; ?></div>
								</li> 
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">生日：</div> <div style="width:150px;float:left"><?php echo $this->_var['home']['user']['birthday']; ?></div>
								</li> 
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">QQ：</div><div style="width:150px;float:left"><?php echo $this->_var['home']['user']['qq']; ?></div>
								</li>  
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">电话：</div>
									<div style="width:150px;float:left"> <?php echo $this->_var['home']['user']['mobile_phone']; ?></div>
									<div><?php if ($this->_var['home']['user']['tstats'] > 0): ?><span style="color:#C00">手机已验证</span><?php endif; ?></div>
								</li> 
								<?php if ($this->_var['home']['user']['tstats'] < 1): ?>
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">手机验证：</div>
									<div><?php if ($this->_var['home']['user']['mobile_phone']): ?><A HREF="#" style="color:#CCC">还没验证手机,点击后验证(暂不能操作)</A><?php else: ?>还没填写手机号<?php endif; ?><div>
								</li> 
								<?php endif; ?>
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">地址：</div> 
									<div style="width:250px;float:left"><?php echo $this->_var['home']['user']['addrs']; ?></div>
								</li>  
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">主页：</div> 
									<div style="width:250px;float:left"><?php echo $this->_var['home']['user']['userhttp']; ?></div>
								</li>  	
								<li style="margin:5px;width:420px;clear:left">
									<div class="user">个人标签：</div>
									<div style="width:250px;float:left"><?php echo $this->_var['home']['user']['usertag']; ?></div>
								</li> 	
<script language="JavaScript">
   
	function email_check(a)
	{
		obj = 'smptemail';  
		var strTemp = "smtp.php?op=usercheck"  + "&id=" + a; 
		//alert(strTemp);
		send_request(strTemp);
	}  
  
</script>