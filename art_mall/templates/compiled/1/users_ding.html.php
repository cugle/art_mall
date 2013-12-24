<?php if ($this->_var['home']['showdds']): ?>
	<li class="line" style="margin:5px;width:615px; height:24px;font-weight: bold; overflow:hidden;"> 
		订单编号：<?php echo $this->_var['home']['showdds']['ddid']; ?> 数量:<?php echo $this->_var['home']['showdds']['pronums']; ?> 金额:<?php echo $this->_var['home']['showdds']['totalmoney']; ?>元 其中物流费用:<?php echo $this->_var['home']['showdds']['wlpay']; ?>元 订单状态:<span style="color:#F00"><?php echo $this->_var['home']['showdds']['statsMessages']; ?></span>
	</li>

	<li class="line" style="margin:5px;width:615px; height:24px;font-weight: bold;overflow:hidden;"> 
		收货信息：姓名:<?php echo $this->_var['home']['showdds']['sh_name']; ?> 电话:<?php echo $this->_var['home']['showdds']['sh_phone']; ?> 地址:<?php echo $this->_var['home']['showdds']['sh_address']; ?> 邮编:<?php echo $this->_var['home']['showdds']['sh_zip']; ?> 
	</li>
	<?php if ($this->_var['home']['showdds']['stats'] < 1): ?>
		<form action="" method="post" name="theForm" style="margin: 0"> 
		<li class="line" style="margin:5px;width:615px; height:24px;font-weight: bold; overflow:hidden;color:#FF6633"> 
			账户余额：<span style="color:#F00"><?php echo $this->_var['home']['user']['money']; ?>元</span> 
			<?php if ($this->_var['home']['user']['money'] >= $this->_var['home']['showdds']['totalmoney']): ?> 
					<INPUT TYPE="submit" value=" 确认支付：<?php echo $this->_var['home']['showdds']['totalmoney']; ?> 元 ">
					<INPUT TYPE="hidden" name="id" value="<?php echo $this->_var['uaddrs']['id']; ?>"> 
					<INPUT TYPE="hidden" name="o" value="ding">
					<INPUT TYPE="hidden" name="ding_ok" value="yes"> 
			<?php else: ?>
				余额不足，不能支付。<A HREF="user.php?o=detail" style="color:#00C">转账汇款</A>
			<?php endif; ?>
		</li>
		</form>
	<?php elseif ($this->_var['home']['showdds']['stats'] == 2): ?>
		<form action="" method="post" name="theForm" style="margin: 0"> 
			<li class="line" style="margin:5px; height:24px; overflow:hidden;">  
				物流公司名:<?php echo $this->_var['home']['showdds']['wlname']; ?>
				物流编号:<?php echo $this->_var['home']['showdds']['wlsn']; ?>  
				<INPUT TYPE="submit" value="确认已到货"> 
				<INPUT TYPE="hidden" name="id" value="<?php echo $this->_var['home']['showdds']['id']; ?>"> 
				<INPUT TYPE="hidden" name="o" value="ding">
				<INPUT TYPE="hidden" name="ding_dhok" value="yes"> 	
			</li>
		</form> 
	<?php elseif ($this->_var['home']['showdds']['stats'] == 3): ?>
		<li class="line" style="margin:5px; height:24px; overflow:hidden;">  
			物流公司名:<?php echo $this->_var['home']['showdds']['wlname']; ?>
			物流编号:<?php echo $this->_var['home']['showdds']['wlsn']; ?>  
		</li>
	<?php endif; ?> 
	<li class="line" style="margin:5px;width:615px; height:24px;font-weight: bold; overflow:hidden;"> 
		<div style="width:70px;text-align:center; height:24px;float:left">产品图片</div>
		<div style="width:100px;text-align:center; height:24px;float:left">编号</div> 
		<div style="width:220px;text-align:center; height:24px;float:left">名称</div>  
		<div style="width:60px;text-align:center; height:24px;float:left">价格</div>
		<div style="width:60px;text-align:center; height:24px;float:left">订购数量</div> 
		<div style="width:60px;text-align:center; height:24px;float:left">小计</div>
	</li> 
 
	<?php $_from = $this->_var['home']['showdds']['ddscarts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'products');if (count($_from)):
    foreach ($_from AS $this->_var['products']):
?>  
		<li class="line" style="margin:5px;width:615px; height:52px; overflow:hidden;" id="carsdel_<?php echo $this->_var['products']['id']; ?>"> 
			<div style="width:70px;text-align:center; height:52px;float:left; overflow:hidden;">
			<a href="<?php echo $this->_var['products']['product_url']; ?>" target="_blank"><img SRC="<?php echo $this->_var['products']['shop_thumb']; ?>"  WIDTH="67" HEIGHT="50" BORDER="0"></a>
			</div>
			<div style="width:100px;text-align:center; height:52px;float:left; overflow:hidden;"><?php echo $this->_var['products']['shop_sn']; ?></div> 
			<div style="width:220px;text-align:center; height:52px;float:left; overflow:hidden;"><A HREF="<?php echo $this->_var['products']['product_url']; ?>" target="_blank"><?php echo $this->_var['products']['name']; ?></A></div>  
			<div style="width:60px;text-align:center; height:52px;float:left"><?php echo $this->_var['products']['shop_price']; ?></div> 
			<div style="width:60px;text-align:center; height:52px;float:left"><?php echo $this->_var['products']['nums']; ?> </div> 
			<div style="width:60px;text-align:center; height:52px;float:left"><?php echo $this->_var['products']['totalprice']; ?> </div>  
		</li> 
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 

<?php else: ?>
	
	<li class="line" style="margin:5px;width:615px; height:24px;font-weight: bold; overflow:hidden;"> 
		<div style="width:150px; height:24px;float:left;text-align: center">编号</div>
		<div style="width:40px; height:24px;float:left;text-align: center">数量</div> 
		<div style="width:80px; height:24px;float:left;text-align: center">金额</div>  
		<div style="width:100px; height:24px;float:left;text-align: center">日期</div> 
		<div style="width:80px; height:24px;float:left;text-align: center">收货姓名</div>   
		<div style="width:100px; height:24px;float:left;text-align: center">状态</div> 
		<div style="width:40px; height:24px;float:left;text-align: center">操作</div>
	</li> 
	<?php $_from = $this->_var['home']['ddid']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ddid');if (count($_from)):
    foreach ($_from AS $this->_var['ddid']):
?> 
		<li class="line" style="margin:5px;width:615px; height:24px; overflow:hidden;" >  
			<div style="width:150px; height:24px;float:left; overflow:hidden;text-align: center;"><a href="user.php?o=ding&so=show&id=<?php echo $this->_var['ddid']['id']; ?>" style="color:#03F"><?php echo $this->_var['ddid']['ddid']; ?></a></div>  
			<div style="width:40px; height:24px;float:left; overflow:hidden;text-align: center;"><?php echo $this->_var['ddid']['pronums']; ?></div>
			<div style="width:80px; height:24px;float:left; overflow:hidden;text-align: center;"><?php echo $this->_var['ddid']['totalmoney']; ?></div> 
			<div style="width:100px; height:24px;float:left; overflow:hidden;text-align: center;"><?php echo $this->_var['ddid']['time']; ?></div> 
			<div style="width:80px; height:24px;float:left; overflow:hidden;text-align: center;" title="电话:<?php echo $this->_var['ddid']['sh_phone']; ?> 地址:<?php echo $this->_var['ddid']['sh_address']; ?> 邮编:<?php echo $this->_var['ddid']['sh_zip']; ?>"><?php echo $this->_var['ddid']['sh_name']; ?></div>  
			<div style="width:100px; height:24px;float:left;text-align: center"><a href="user.php?o=ding&so=show&id=<?php echo $this->_var['ddid']['id']; ?>" style="color:#03F"><?php echo $this->_var['ddid']['statsMessages']; ?></a></div> 
			<div style="width:40px; height:24px;float:left; overflow:hidden;text-align: center;"><?php if ($this->_var['ddid']['stats'] < 1): ?><a href="user.php?o=ding&so=del&id=<?php echo $this->_var['ddid']['id']; ?>">删除</a><?php endif; ?></div>  
		</li>  
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<li style="margin:2px 0 0 0;color:#0066FF;font-weight: bold; padding-left:5px ;background-color:#F5F5F5;width:615px;">  
		<span class="fenye" style="margin:1px;padding: 1px"  > <?php echo $this->_var['home']['showpage']; ?> </span >  
	</li> 
	
<?php endif; ?> 