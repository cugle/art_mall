<li  class="line" style="margin:1px;width:616px; height:108px;overflow:hidden;">
	<div style="color:#0066FF;font-weight: bold; padding-left:5px;background-color:#E3E3E3;">填写汇款信息</div>  
		<FORM action="" method="post" name="theForm" onsubmit="return validate();">
			<div style="width:600px">
				<div style="width:300px;float:left">
					<div style="float:left;width:100px;text-align: right;font-weight: bold;">付款方式：</div>
					<div  style="float:left;width:200px;"><INPUT TYPE="text" NAME="bankname"  value="" style="width:100px">如:农业银行</div>  
				</div>
				<div style="width:300px;float:left">
					<div style="float:left;width:60px;text-align: right;font-weight: bold;">金额：</div>
					<div  style="float:left;width:200px;"><INPUT TYPE="text" NAME="remmoney"  value="" style="width:58px"></div> 
					
				</div>				
			</div>

			<div style="width:600px">
				<div style="width:300px;float:left">
					 <div style="float:left;width:100px;text-align: right;font-weight: bold;">付款人姓名：</div>
					<div  style="float:left;width:200px"><INPUT TYPE="text" NAME="payname" value="" style="width:50px"></div> 
					
				</div>
				<div style="width:300px;float:left">
					<div style="float:left;width:60px;text-align: right;font-weight: bold;">交易号：</div>
					<div  style="float:left;width:200px;"><INPUT TYPE="text" NAME="paynums"  value="" style="width:180px"> </div> 
					
				</div>				
			</div>
			<div style="width:600px">  
				<INPUT TYPE="submit" value="确定提交" style="margin: 5px 0 0 100px"> 
				<INPUT TYPE="hidden" name="o" value="<?php echo $this->_var['home']['user_o']; ?>">  
			</div>
		</form> 
</li>
<?php if ($this->_var['home']['detail']): ?>
	<li style="color:#0066FF;font-weight: bold; padding-left:5px ;background-color:#E3E3E3;width:612px;">汇款明细,合计:<?php echo $this->_var['home']['T_remmoney']; ?>元</li> 
	<li class="line" style="margin:5px;width:615px; height:24px;font-weight: bold; overflow:hidden;">
		<div style="width:100px; height:24px;float:left">付款方式</div>
		<div style="width:60px; height:24px;float:left">金额</div> 
		<div style="width:80px; height:24px;float:left">姓名</div>  
		<div style="width:160px; height:24px;float:left">交易号</div> 
		<div style="width:120px; height:24px;float:left">时间</div> 
		<div style="width:40px; height:24px;float:left">状态</div> 
	</li> 

	<?php $_from = $this->_var['home']['detail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'detail');if (count($_from)):
    foreach ($_from AS $this->_var['detail']):
?> 
		<li class="line" style="margin:5px;width:615px; height:24px; overflow:hidden;">
			<div style="width:100px; height:24px;float:left; overflow:hidden;"><?php echo $this->_var['detail']['bankname']; ?></div>
			<div style="width:60px; height:24px;float:left; overflow:hidden;"><?php echo $this->_var['detail']['remmoney']; ?></div> 
			<div style="width:80px; height:24px;float:left; overflow:hidden;"><?php echo $this->_var['detail']['payname']; ?></div>  
			<div style="width:160px; height:24px;float:left"><?php echo $this->_var['detail']['paynums']; ?></div> 
			<div style="width:120px; height:24px;float:left"><?php echo $this->_var['detail']['dateadd']; ?></div> 
			<div style="width:40px; height:24px;float:left;"><?php if ($this->_var['detail']['checked']): ?><div style="color:#33CC00">已到帐</div><?php else: ?>未确认<?php endif; ?></div> 
		</li> 
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

	<li style="margin:2px 0 0 0;color:#0066FF;font-weight: bold; padding-left:5px ;background-color:#F5F5F5;width:615px;">  
		<span class="fenye" style="margin: 1px;"  > <?php echo $this->_var['home']['showpage']; ?> </span >  
	</li>  

<?php endif; ?> 

<script language="JavaScript">
  
	function validate() {
		var frm   = document.forms['theForm']; 
		var bankname  = frm.elements['bankname'].value;
		var remmoney  = frm.elements['remmoney'].value;
		var payname = frm.elements['payname'].value;
		var paynums   = frm.elements['paynums'].value; 

		var msg = '';
		var reg = null;

		if( bankname.length < 2){
			msg += '付款方式' + '\n';
		}

		if( remmoney.length < 0.01){
			msg += '请输入金额' + '\n';
		}

		if (payname.length < 2 || payname.length > 10){ 
			msg += '付款人姓名' + '\n'; 
		}

		if (paynums.length < 2 ){ 
			msg += '请输入交易号' + '\n'; 
		}

		if (msg.length > 0){
			alert(msg);
			return false;
		}else
		{
			return true;
		}
	}

  
</script>