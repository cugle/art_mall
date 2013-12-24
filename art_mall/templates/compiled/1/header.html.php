<div class="header">

  <div class="logo"><a href="/index.php"><img src="<?php echo $this->_var['template_path']; ?>images/logo.jpg" border="0" /></a></div>
  <div id="menu">
    <ul id="nav">
      
      <li  ><span class="note"> </span></li> 
       
	
											<?php if ($this->_var['home']['pro_fid'] < 1): ?>
												<?php $_from = $this->_var['home']['Productcat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'productcat');if (count($_from)):
    foreach ($_from AS $this->_var['productcat']):
?>  
														  
														<li class="mainlevel" id="mainlevel_01"><?php if ($this->_var['productcat']['next_node']): ?><a href="<?php echo $this->_var['productcat']['name_url']; ?>"><img src="/data/brandlogo/<?php echo $this->_var['productcat']['pro_interval']; ?>" width="105" height="56" /></a><?php else: ?><img src="/data/brandlogo/<?php echo $this->_var['productcat']['pro_interval']; ?>" width="105" height="56" /><?php endif; ?>
														<ul class="sub_nav_01">
    												    <span class="Triangle_con"></span>
														<?php if ($this->_var['productcat']['next_node']): ?>
														<?php $_from = $this->_var['productcat']['next_node']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'next_node');if (count($_from)):
    foreach ($_from AS $this->_var['next_node']):
?> 
															<?php if ($this->_var['next_node']['ifnav']): ?><li><?php echo $this->_var['next_node']['sub_next']; ?></li><?php endif; ?>
														<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
														<?php endif; ?>
														 </ul>
	  </li>
												<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
											
											<?php endif; ?>
											
	   
	   

    </ul>
  </div>
</div>