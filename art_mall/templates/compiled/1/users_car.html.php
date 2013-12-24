 <li class="line" style="margin:5px;width:730px; height:24px;font-weight: bold; overflow:hidden;"> 
	<div style="width:70px;text-align:center; height:24px;float:left">产品图片</div>
	<div style="width:100px;text-align:center; height:24px;float:left">编号</div> 
	<div style="width:320px;text-align:center; height:24px;float:left">名称</div>  
	<div style="width:60px;text-align:center; height:24px;float:left">价格</div>
	<div style="width:60px;text-align:center; height:24px;float:left">订购数量</div>  
	<div style="width:60px;text-align:center; height:24px;float:left">库存数量</div>
	<div style="width:60px;text-align:center; height:24px;float:left">删除</div> 
</li> 

<?php if ($this->_var['home']['car']): ?> 
	<FORM action="" method="post" name="theForm" onsubmit="return validate();" style="margin: 0">
	<?php $_from = $this->_var['home']['car']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'products');if (count($_from)):
    foreach ($_from AS $this->_var['products']):
?>  
		<li class="line" style="margin:5px;width:735px; height:52px; overflow:hidden;" id="carsdel_<?php echo $this->_var['products']['id']; ?>"> 
			<div style="width:70px;text-align:center; height:52px;float:left; overflow:hidden;">
			<a href="<?php echo $this->_var['products']['product_url']; ?>" target="_blank"><img SRC="<?php echo $this->_var['products']['shop_thumb']; ?>"  WIDTH="67" HEIGHT="50" BORDER="0"></a>
			</div>
			<div style="width:100px;text-align:center; height:52px;float:left; overflow:hidden;"><?php echo $this->_var['products']['shop_sn']; ?></div> 
			<div style="width:320px;text-align:center; height:52px;float:left; overflow:hidden;"><A HREF="<?php echo $this->_var['products']['product_url']; ?>" target="_blank"><?php echo $this->_var['products']['name']; ?></A></div>  
			<div style="width:60px;text-align:center; height:52px;float:left"><?php echo $this->_var['products']['shop_price']; ?></div> 
			<div style="width:60px;text-align:center; height:52px;float:left"><INPUT TYPE="text" NAME=prids[<?php echo $this->_var['products']['prid']; ?>] value="<?php echo $this->_var['products']['nums']; ?>" style="width:20px"></div>   
			<div style="width:60px;text-align:center; height:52px;float:left"><?php echo $this->_var['products']['nums']; ?></div>  
			<div style="width:60px;text-align:center; height:52px;float:left;cursor:pointer;" onMouseDown="if(confirm('删除')) cardel(<?php echo $this->_var['products']['id']; ?>,'carsdel')">删除</div> 
		</li> 
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<li style="height:24px;float:right"><A HREF="user.php?o=car&del=all" style="color:#FF9900">清空购物车>></A></li>
	 
	<li style="font-weight: bold">请确认收货地址后提交订单：</li> 
		<?php $_from = $this->_var['home']['uaddrs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'uaddrs');if (count($_from)):
    foreach ($_from AS $this->_var['uaddrs']):
?> 
			<li class="line" style="margin:5px;width:735px; height:24px; overflow:hidden;" > 
				<div style="width:380px; height:24px;float:left; overflow:hidden;"><?php echo $this->_var['uaddrs']['ccid']; ?><?php echo $this->_var['uaddrs']['addrs']; ?></div>  
				<div style="width:100px; height:24px;float:left">邮编:<?php echo $this->_var['uaddrs']['zip']; ?></div> 
				<div style="width:60px; height:24px;float:left; overflow:hidden;"><?php echo $this->_var['uaddrs']['name']; ?></div>
				<div style="width:90px; height:24px;float:left; overflow:hidden;"><?php echo $this->_var['uaddrs']['tel']; ?></div> 
				<div style="width:40px; height:24px;float:left" ><INPUT style="margin-top:5px;" TYPE="radio" name="uaddrs" value="<?php echo $this->_var['uaddrs']['id']; ?>" NAME="默认" <?php if ($this->_var['uaddrs']['type'] == 1): ?>checked<?php endif; ?> ></div>  
			</li> 
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<li style="height:24px;float:right"><A HREF="user.php?o=addrs" style="color:#6666FF">增加新的收货地址>></A></li>
	<li style="margin:1px;color:#0066FF;  padding:1px;width:617px;height:24px;"> 
		  <INPUT TYPE="hidden" value="yes" name="ding">
		  <INPUT TYPE="hidden" value="car" name="o">
		  <INPUT TYPE="submit" value="确认提交订单" style="margin-left:10px;color:#6600FF">
	</li>
	</form>
<?php endif; ?>
<script language="JavaScript">
  
	function cardel(a,op)
	{ 
		obj = op + "_" + a;
		if(op == 'carsdel'){
			document.getElementById(obj).style.display="none";
		}
		var strTemp = "ajax_command.php?id=" + a + "&op=" + op;  
		send_request(strTemp);
	}
  
</script>