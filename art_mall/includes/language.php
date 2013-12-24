<?php

$Aconf['OSUNIT_VERSION']  = 'OSUNIT_V1.2.130515';
//网站地址部分,array( '首页','index.php',true); 第三个参数为是否后台显示 
$Aweb_url["index"]       = array( 'home','index.php',         'target="_self"',true);
$Aweb_url["webreg"]      = array( 'Register 网站注册','webreg.php','target="_self"',true);
$Aweb_url["support"]     = array( 'guestbook 访客留言','support.php',   'target="_self"',true);
$Aweb_url["articles"]    = array( 'blog/news','articles.php',  'target="_self"',true);
$Aweb_url["article"]     = array( 'detail','article.php',       'target="_self"');
$Aweb_url["acomms"]      = array( '新闻评论','acomms.php',    'target="_self"',true);

$Aweb_url["products"]    = array( 'artist','products.php',  'target="_self"',true); 
$Aweb_url["product"]     = array( '产品详情','product.php',   'target="_self"');
$Aweb_url["procomms"]    = array( '产品评论','procomms.php',  'target="_self"');
$Aweb_url["procomp"]     = array( '产品比较','procomp.php',   'target="_self"');

$Aweb_url["search"]      = array( 'Search 搜索','search.php',    'target="_self"',true); 
$Aweb_url["pravail"]     = array( '商家列表','pravail.php',   'target="_self"',true); 
$Aweb_url["brands"]      = array( '品牌列表','brands.php',    'target="_self"',true); 
$Aweb_url["votes"]       = array( '网上调查','votes.php',     'target="_self"',true);
$Aweb_url["vote"]        = array( '调查详情','vote.php',      'target="_self"');

$Aweb_url["links"]       = array( '友情连接','links.php',     'target="_self"',true);
$Aweb_url["about"]       = array( 'about us','about.php',     'target="_self"',true); 
$Aweb_url["sernet"]       = array( '服务网络','sernet.php',     'target="_self"',true);
$Aweb_url["jobadd"]       = array( '简历申请','jobadd.php',     'target="_self"',true);

$Aweb_url["user"]        = array( 'User center 用户中心','user.php');
$Aweb_url["map"]         = array( 'RSS聚合频道','map.php',    'target="_self"',true);
$Aweb_url["pic"]         = array( '图库','pic.php',           'target="_self"',true);
$Aweb_url["pics"]         = array( '图库列表','pics.php',           'target="_self"',true);
$Aweb_url["support"]     = array( '客服留言','support.php',   'target="_self"',true); 
$Aweb_url["vip"]         = array( 'VIP客户','vip.php',        'target="_self"',true);
$Aweb_url["sernet"]      = array( '服务网络','sernet.php',        'target="_self"',true);
$Aweb_url["package"]     = array( '建站套餐','package.php',        'target="_self"',true);

//文字提示描述
$Aweb_desc["article_del"] = '此新闻没通过审核，请稍后再试'; 
$Aweb_desc["sys_url_error"] = '当前域名不存在,<A HREF="'.$Aconf['domain_url'].'">返回主站首页>></A>'; 
$Aweb_desc["sys_url_title"] = '域名不存在';
$Aweb_desc["sys_dir_chmod"] = '请查阅目录权限 ';
$Aweb_desc["stype"][1] = '文章';
$Aweb_desc["stype"][2] = '产品';
$Aweb_desc["stype"][3] = '商家';


//后台及前台 首页 文章列表 产品列表页的项目显示选项设置
$Aitname["notices"]  = '公告';
$Aitname["descs"]    = '公司简介';
$Aitname["tjcat"]    = 'FLASH_推荐';
$Aitname["vote"]     = '推荐调查'; 
$Aitname["votes"]    = '调查列表'; 
$Aitname["keytj"]    = '关键词推荐'; 
$Aitname["links"]    = '友情连接列表'; 
$Aitname["1----"]    = '------------'; 

$Aitname["qq"]       = '在线QQ';
$Aitname["sesspro"]  = '最近访问'; 
$Aitname["vip"]      = 'VIP客户';
$Aitname["users"]    = '新注册用户';
$Aitname["lineusers"]= '在线用户';
$Aitname["2----"]    = '------------';
$Aitname["articat"]          = '新闻类别';
$Aitname["articles"]         = 'blog/news';
$Aitname["articles_top"]     = '置顶新闻';
$Aitname["articles_focus"]   = '焦点新闻';
$Aitname["articles_trundle"] = '滚动新闻'; 
$Aitname["articles_ifpic"]   = '新闻图库';
$Aitname["articles_comms"]   = '评论最多的文章';
$Aitname["3----"]    = '------------';
$Aitname["productcat"]       = '商品类别'; 
$Aitname["products"]         = '商品列表'; 
$Aitname["products_top"]     = '促销商品';
$Aitname["products_special"] = '畅销商品';
$Aitname["4----"]    = '------------';
$Aitname["probrand"]	     = '推荐品牌'; 
$Aitname["pravail"]          = '推荐经销商';  

$Aitname["acids"]            = '新闻子分类';
 
?>
