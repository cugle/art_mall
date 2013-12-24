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
</style>

<script src="js/jquery-1.7.2.min.js" ></script>

<script>

/**

 ************************************************************

 ***@project jquery瀑布流插件

 ***@author hcp0209@gmail.com

 ***@ver version 1.0

 ************************************************************

 */

;(function($){

   var 

   //参数

   setting={

      column_width:204,//列宽

	  column_className:'waterfall_column',//列的类名

	  column_space:10,//列间距

	  cell_selector:'.cell',//要排列的砖块的选择器，context为整个外部容器

	  img_selector:'img',//要加载的图片的选择器

	  auto_imgHeight:true,//是否需要自动计算图片的高度

	  fadein:true,//是否渐显载入

	  fadein_speed:600,//渐显速率，单位毫秒

	  insert_type:1, //单元格插入方式，1为插入最短那列，2为按序轮流插入

	  getResource:function(index){}  //获取动态资源函数,必须返回一个砖块元素集合,传入参数为加载的次数

   },

   //

   waterfall=$.waterfall={},//对外信息对象

   $container=null;//容器

   waterfall.load_index=0, //加载次数

   $.fn.extend({

       waterfall:function(opt){

		  opt=opt||{};  

	      setting=$.extend(setting,opt);

		  $container=waterfall.$container=$(this);

		  waterfall.$columns=creatColumn();

		  render($(this).find(setting.cell_selector).detach(),false); //重排已存在元素时强制不渐显

		  waterfall._scrollTimer2=null;

		  $(window).bind('scroll',function(){

		     clearTimeout(waterfall._scrollTimer2);

			 waterfall._scrollTimer2=setTimeout(onScroll,300);

		  });

		  waterfall._scrollTimer3=null;

		  $(window).bind('resize',function(){

		     clearTimeout(waterfall._scrollTimer3);

			 waterfall._scrollTimer3=setTimeout(onResize,300);

		  });

	   }

   });

   function creatColumn(){//创建列

      waterfall.column_num=calculateColumns();//列数

	  //循环创建列

	  var html='';

	  for(var i=0;i<waterfall.column_num;i++){

	     html+='<div class="'+setting.column_className+'" style="width:'+setting.column_width+'px; display:inline-block; *display:inline;zoom:1; margin-left:'+setting.column_space/2+'px;margin-right:'+setting.column_space/2+'px; vertical-align:top; overflow:hidden"></div>';

	  }

	  $container.prepend(html);//插入列

	  return $('.'+setting.column_className,$container);//列集合

   }

   function calculateColumns(){//计算需要的列数

      var num=Math.floor(($container.innerWidth())/(setting.column_width+setting.column_space));

	  if(num<1){num=1;} //保证至少有一列

	  return num;

   }

   function render(elements,fadein){//渲染元素

      if(!$(elements).length) return;//没有元素

      var $columns = waterfall.$columns;

      $(elements).each(function(i){										

		  if(!setting.auto_imgHeight||setting.insert_type==2){//如果给出了图片高度，或者是按顺序插入，则不必等图片加载完就能计算列的高度了

		     if(setting.insert_type==1){ 

			    insert($(elements).eq(i),setting.fadein&&fadein);//插入元素

			 }else if(setting.insert_type==2){

			    insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素	 

		     }

			 return true;//continue

		  }						

		  if($(this)[0].nodeName.toLowerCase()=='img'||$(this).find(setting.img_selector).length>0){//本身是图片或含有图片

		      var image=new Image;

			  var src=$(this)[0].nodeName.toLowerCase()=='img'?$(this).attr('src'):$(this).find(setting.img_selector).attr('src');

			  image.onload=function(){//图片加载后才能自动计算出尺寸

			      image.onreadystatechange=null;

				  if(setting.insert_type==1){ 

				     insert($(elements).eq(i),setting.fadein&&fadein);//插入元素

				  }else if(setting.insert_type==2){

					 insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素	 

				  }

				  image=null;

			  }

			  image.onreadystatechange=function(){//处理IE等浏览器的缓存问题：图片缓存后不会再触发onload事件

			      if(image.readyState == "complete"){

					 image.onload=null;

					 if(setting.insert_type==1){ 

					    insert($(elements).eq(i),setting.fadein&&fadein);//插入元素

					 }else if(setting.insert_type==2){

					    insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素	 

					 }

					 image=null;

				  }

			  }

			  image.src=src;

		  }else{//不用考虑图片加载

		      if(setting.insert_type==1){ 

				 insert($(elements).eq(i),setting.fadein&&fadein);//插入元素

			  }else if(setting.insert_type==2){

				 insert2($(elements).eq(i),i,setting.fadein&&fadein);//插入元素	 

			  }

		  }						

	  });

   }

   function public_render(elems){//ajax得到元素的渲染接口

   	  render(elems,true);	

   }

   function insert($element,fadein){//把元素插入最短列

      if(fadein){//渐显

	     $element.css('opacity',0).appendTo(waterfall.$columns.eq(calculateLowest())).fadeTo(setting.fadein_speed,1);

	  }else{//不渐显

         $element.appendTo(waterfall.$columns.eq(calculateLowest()));

	  }

   }

   function insert2($element,i,fadein){//按序轮流插入元素

      if(fadein){//渐显

	     $element.css('opacity',0).appendTo(waterfall.$columns.eq(i%waterfall.column_num)).fadeTo(setting.fadein_speed,1);

	  }else{//不渐显

         $element.appendTo(waterfall.$columns.eq(i%waterfall.column_num));

	  }

   }

   function calculateLowest(){//计算最短的那列的索引

      var min=waterfall.$columns.eq(0).outerHeight(),min_key=0;

	  waterfall.$columns.each(function(i){						   

		 if($(this).outerHeight()<min){

		    min=$(this).outerHeight();

			min_key=i;

		 }							   

	  });

	  return min_key;

   }

   function getElements(){//获取资源

      $.waterfall.load_index++;

      return setting.getResource($.waterfall.load_index,public_render);

   }

   waterfall._scrollTimer=null;//延迟滚动加载计时器

   function onScroll(){//滚动加载

      clearTimeout(waterfall._scrollTimer);

	  waterfall._scrollTimer=setTimeout(function(){

	      var $lowest_column=waterfall.$columns.eq(calculateLowest());//最短列

		  var bottom=$lowest_column.offset().top+$lowest_column.outerHeight();//最短列底部距离浏览器窗口顶部的距离

		  var scrollTop=document.documentElement.scrollTop||document.body.scrollTop||0;//滚动条距离

		  var windowHeight=document.documentElement.clientHeight||document.body.clientHeight||0;//窗口高度

		  if(scrollTop>=bottom-windowHeight){

			 render(getElements(),true);

		  }

	  },100);

   }

   function onResize(){//窗口缩放时重新排列

      if(calculateColumns()==waterfall.column_num) return; //列数未改变，不需要重排

      var $cells=waterfall.$container.find(setting.cell_selector);

	  waterfall.$columns.remove();

	  waterfall.$columns=creatColumn();

      render($cells,false); //重排已有元素时强制不渐显

   }

})(jQuery);

</script>
</head>

<body>
<?php echo $this->fetch('header.html'); ?> 
<div class="main">
  
  
  <div class="mainbody">
     <div class="top"><ul><?php echo $this->_var['home']['nowNave']; ?></ul></div>
    <div class="maincontent">
	 
<div id="container">



							<?php if ($this->_var['home']['products_count'] > 0): ?>
								<?php $_from = $this->_var['home']['products']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'products');if (count($_from)):
    foreach ($_from AS $this->_var['products']):
?> 
										<div class="cell"><div  class="cellimg">
										<a href="<?php echo $this->_var['products']['product_url']; ?>"><img src="<?php echo $this->_var['products']['min_thumb']; ?>" /></a>
										<a href="<?php echo $this->_var['products']['product_url']; ?>"><p class="celltitle"><?php echo $this->_var['products']['name']; ?></p></a></div><p class="celltext"><a href="<?php echo $this->_var['products']['product_url']; ?>"><?php echo $this->_var['products']['edit_comm']; ?></a></p> </div>

								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							<?php else: ?>
							   <div style="color:#FF0000;margin:5px;padding:5px;font-size: 30px;background-color: #000000">此查询条件下无商品</div>
							<?php endif; ?>






 

</div>
	 </div>
  </div>
  
  <div class="footer"></div>
</div>
<?php echo $this->fetch('footer.html'); ?> 





<script>

var opt={

  getResource:function(index,render){//index为已加载次数,render为渲染接口函数,接受一个dom集合或jquery对象作为参数。通过ajax等异步方法得到的数据可以传入该接口进行渲染，如 render(elem)
  	  if(index>=7) return;
	  
	  index=index+1;

	  var html='';


  		$.getJSON("./jsonproducts.php", {id:<?php echo $this->_var['home']['pcid']; ?>, page: index },function(result){	
			$.each(result, function(i, field){
			  
			html+='<div class="cell"><div  class="cellimg"><a href="'+field.product_url+'"><img src="'+field.min_thumb+'" /></a><a href="'+field.product_url+'"><p class="celltitle">'+field.name+'</p></a></div><p class="celltext"><a href="'+field.product_url+'">'+field.edit_comm+'</a></p> </div>';
			});

		render(html)
    	});

  },

  auto_imgHeight:true,

  insert_type:1

}

$('#container').waterfall(opt);

</script>

</body>
</html>
