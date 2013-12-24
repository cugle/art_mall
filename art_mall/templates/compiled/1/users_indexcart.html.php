<?php if ($this->_var['home']['car']): ?>
<FORM action="" method="post" name="theForm" onsubmit="return validate();" style="margin: 5px">
	<?php $_from = $this->_var['home']['car']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'products');if (count($_from)):
    foreach ($_from AS $this->_var['products']):
?>  
		<li class="line" style="margin:0px;width:180px; height:25px; overflow:hidden;" id="carsdel_<?php echo $this->_var['products']['id']; ?>">
		  <div style="width:100px;text-align:center; height:25px;float:left; overflow:hidden;"><A HREF="<?php echo $this->_var['products']['product_url']; ?>" target="_blank" style="color:#FFFFFF" ><?php echo $this->_var['products']['name']; ?></A></div>  
			<div style="width:80px;text-align:center; height:25px;float:left"><?php echo $this->_var['products']['nums']; ?></div>   
  </li> 
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<li style="height:25px;float:right;margin:5px;"><A HREF="user.php?o=car" style="color:#FF9900">进入购物车>></A></li>
	 
	 
</form>
<?php endif; ?>
 