<?php 
/* 显示的模块 */
if (!defined('IN_OUN')) {
    die('Hacking attempt');
} 
/* 基本配置信息 */
$n = 0;
$Asets = explode("]",$Aconf['home']);   
foreach ($Asets as $v) {
   $At = array();
   $At = explode("|",$v); 
   if($At[0] == 'one') {
	   $Atv = explode(";",$At[1]);
	   foreach($Atv as $val) {
		   $Atmp = explode(":",$val);
		   if($Atmp[0]) {
			   $Aitem["one"][$Atmp[0]] = explode(",",$Atmp[1]);
			}
		}
	}
   if($At[0] == 'two')
	{
	   $Atv = explode(";",$At[1]);
	   foreach($Atv as $val) {
		   $Atmp = explode(":",$val);
		   if($Atmp[0]) {
			   $Aitem["two"][$Atmp[0]] = explode(",",$Atmp[1]);  
			}
		}
	}

   if($At[0] == 'three') {  
       $Atv = explode(";",$At[1]);
       foreach($Atv as $val) {
           $Atmp = explode(":",$val);
           if($Atmp[0]) {
               $Aitem["three"][$Atmp[0]] = explode(",",$Atmp[1]);     
               $Aitem["three"][$Atmp[0]][2] = str_replace("~", ',',$Aitem["three"][$Atmp[0]][2]);
            }
        }
    }
}//forreach($Asets as $v)
/* one  start */
 //公告 
if($Aitem["one"]["notices"][0]){
	$Aconf["notices_title"] = $Aitem["one"]["notices"][3]; 
	$Aconf["notices_logo"] = $Aitem["one"]["notices"][4];
}else{
	$Aconf["notices"] = false;
}
 //公司简介
if($Aitem["one"]["descs"][0]){
	$Aconf["descs_title"] = $Aitem["one"]["descs"][3]; 
	$Aconf["descs_logo"]  = $Aitem["one"]["descs"][4];
}else{
	$Aconf["descs"] = false;
}
 //新闻分类,只得到二级分类
if($Aitem["one"]["articat"][0]){ 
	$n = 0;
	$Ahome["Articat_title"] = $Aitem["one"]["articat"][3];  
	$Ahome["Articat_logo"] = $Aitem["one"]["articat"][4];
	$row = $oPub->select("SELECT acid,name,next_node FROM ".$pre."articat where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC"); 
	while( @list( $key, $val ) = @each( $row ) ) {
		$row[$key]['name'] = $val["name"];
		if($Aconf['rewrite']){
			$row[$key]['name_url'] = 'articles-'.$val["acid"].'-0.html';
		}else{
			$row[$key]['name_url'] = 'articles.php?id='.$val["acid"]; 
		}
		//得到下级分类
		$row[$key]["next_node"] = '';
		$tmpid  = next_node_all($val["acid"], $pre."articat","acid",false);
		if($tmpid){
			$Atmp = explode(",",$tmpid);
			$tmpid = '';
			while( @list( $keyx, $valx ) = @each( $Atmp ) ) {
				if($valx > 0 ){
					$tmpid  .=$valx.',';
				}
			}
			if($tmpid){
				$tmpid = substr($tmpid,0,-1);
			} 
		} 
		if($tmpid){ 
			$sql = "SELECT acid,name FROM ".$pre."articat 
			where acid in($tmpid)   
			AND domain_id=".$Aconf['domain_id']."   
			ORDER BY acid ASC";
			$rownext = $oPub->select($sql); 
			$str = '';
			while( @list( $keyy, $valy ) = @each( $rownext ) ) {
				if($Aconf['rewrite']){
					$str = '<a href="articles-'.$valy["acid"].'-0.html">'.$valy["name"].'</a>';
				}else{
					$str= '<a href="articles.php?id='.$valy["acid"].'">'.$valy["name"].'</a>';
				}
				$rownext[$keyy]["sub_next"] = $str;
			}
			$row[$key]["next_node"] = $rownext; 
		}
		$n ++;
	}
	$Ahome["Articat"] = $row;
	$Ahome["Articat_key"] = $n; 
}else{
	$Ahome["Articat"] = false;
} 
 //商品分类
if($Aitem["one"]["productcat"][0]){
	$n = 0;
	$Ahome["Productcat_title"] = $Aitem["one"]["productcat"][3];
	$Ahome["Productcat_logo"] = $Aitem["one"]["productcat"][4];
	$row = $oPub->select("SELECT pcid,name,next_node,pro_interval FROM ".$pre."productcat where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY pcid ASC");  
	while( @list( $key, $val ) = @each( $row ) ) {
		$row[$key]['name'] = $val["name"];
		$row1[$key]['pro_interval'] = $val["pro_interval"];
		if($Aconf['rewrite']){
				$row[$key]['name_url'] = 'products-'.$val["pcid"].'-0-0-0-0.html';
		}else{
				$row[$key]['name_url'] = 'products.php?id='.$val["pcid"]; 
		}
		//得到下级分类
		$row[$key]["next_node"] = '';
		$tmpid  = next_node_all($val["pcid"], $pre."productcat","pcid",false);
		if($tmpid){
			$Atmp = explode(",",$tmpid);
			$tmpid = '';
			while( @list( $keyx, $valx ) = @each( $Atmp ) ) {
				if($valx > 0 ){
					$tmpid  .=$valx.',';
				}
			}
			if($tmpid){
				$tmpid = substr($tmpid,0,-1);
			} 
		} 
		if($tmpid){ 
			$sql = "SELECT pcid,name,pro_interval,ifnav FROM ".$pre."productcat 
			where pcid in($tmpid)   
			AND domain_id=".$Aconf['domain_id']."   
			ORDER BY pcid ASC";
			$rownext = $oPub->select($sql); 
			$str = '';
			while( @list( $keyy, $valy ) = @each( $rownext ) ) {
				if($Aconf['rewrite']){
					$str = '<a href="products-'.$valy["pcid"].'-0-0-0-0.html">'.$valy["name"].'</a>';
				}else{
					$str= '<a href="products.php?id='.$valy["pcid"].'">'.$valy["name"].'</a>';
				}
				$rownext[$keyy]["sub_next"] = $str;
				$rownext[$keyy]["ifnav"] = $valy["ifnav"];//add by cg
			}
			$row[$key]["next_node"] = $rownext; 
		}
		$n ++ ;
	}
	$Ahome["Productcat"] = $row;
	$Ahome["Productcat_key"] = $n;
}else{
	$Ahome["Productcat"] = false;
}
 //推荐调查
if($Aitem["one"]["vote"][0]){
	$Ahome["vote_title"] = $Aitem["one"]["vote"][3]; 
	$Ahome["vote_logo"] = $Aitem["one"]["vote"][4]; 
	$Ahome["vote"] = false;
	$sql = "SELECT a.vtid,a.vt_name,a.vt_desc FROM ".$pre."vote_title AS a 
		WHERE a.states=0 AND a.is_show = 1  AND	 a.domain_id =  ".$Aconf['domain_id']."   order by top desc,vtid desc limit 1";
	$row = $oPub->getRow($sql);
	$Ahome["vote"]["vote_title"] = $row;
	if($row) {  
		$Ahome["vote"]["for_vote_group"]= vote($row["vtid"]); 
	}  
}else{
	$Ahome["vote"] = false;
} 
// 在线QQ
if($Aitem["one"]["qq"][0]){
	$Ahome["qq_title"] = $Aitem["one"]["qq"][3];
	$Ahome["qq_logo"] = $Aitem["one"]["qq"][4];
	$Ahome["qq"] = false;
	$row = $oPub->select("SELECT * FROM ".$pre."qq  WHERE   domain_id =  ".$Aconf['domain_id']);
	while( @list( $key, $val ) = @each( $row ) ) {
		$row[$key]["qq_name"] = !empty($val["qq_name"])?$val["qq_name"]:$val["qq"];
	}
	$Ahome["qq"] = $row;
}else{
	$Ahome["qq"] = false;
} 

/* 最近浏览的商品 start */
if($Aitem["one"]["sesspro"][0]){
	//清除缓存 start 
	$Ahome["Sesspro_title"] = $Aitem["one"]["sesspro"][3];
	$Ahome["Sesspro_logo"] = $Aitem["one"]["sesspro"][4];
	if($userid > 0)
	{
		$sesspro = $oPub->select('SELECT prid FROM '. $pre.'sesspro WHERE userid = "'.$userid.'" and domain_id="'.$Aconf['domain_id'].'" order by expiry desc limit 6'); 
	}else
	{
		$sesspro = $oPub->select('SELECT prid FROM '. $pre.'sesspro WHERE sesskey = "'.SESS_ID.'" and domain_id="'.$Aconf['domain_id'].'"   order by expiry desc limit 6');
	} 
	if($sesspro)
	{
		while( @list( $k, $v) = @each($sesspro) )
		{ 
			$sesspro[$k]['sesspro'] = $oPub->getRow("SELECT a.prid, a.name, a.shop_price, a.s_discount,a.min_thumb, a.shop_thumb  FROM  ".$pre."producttxt as a WHERE  a.prid = ".$v['prid']." LIMIT 1"); 

			if($Aconf['rewrite']){
				$sesspro[$k]['sesspro']['product_url']  = 'product-'.$v['prid'].'.html'; 
			}else{
				$sesspro[$k]['sesspro']['product_url'] =  'product.php?id='.$v['prid'];
			} 

		} 
	} 
	$Ahome["Sesspro"] = $sesspro;
}else{
	$Ahome["Sesspro"] = false;
} 
/* 最近浏览的商品 end */
/* 购物车 数量 start */ 
if($_SESSION['user_id'] > 0 )
{
	$Ahome["cartsNum"] = $oPub->getOne('SELECT count(*)  FROM '.$pre.'carts WHERE users_id  = "'.$_SESSION['user_id'].'" AND domain_id = "'.$Aconf['domain_id'].'"'); 
}  
/* 购物车 数量 end */

/* two start  */
//推荐网站
if($Aitem["two"]["vip"][0]){
	$Ahome["Vip_title"] = $Aitem["two"]["vip"][3]; 
	$Ahome["Vip_logo"] = $Aitem["two"]["vip"][4]; 
	$limits=$Aitem["two"]["vip"][1];  
	$row = $oPub->select("SELECT main_domin,header_title,shop_logo  FROM ".$pre."sysconfig where states<>1  ORDER BY states desc limit ".$limits); 
	while( @list( $key, $val ) = @each( $row ) ) {
			if($val['shop_logo'] == '') {
				$rand_array=range(1,50); 
				$row[$k]['shop_logo'] = 'images/viplogo/osunit_'.$rand_array[$k].'.jpg';
			}else{
				$row[$k]['shop_logo'] = 'data/weblogo/'.$val['shop_logo'];
			}
	} 
	$Ahome["Vip"] = $row;
}else{
	$Ahome["Vip"] = false;
}
//新闻列表
if($Aitem["two"]["articles"][0]){
	$Ahome["Tarticles_title"] = $Aitem["two"]["articles"][3]; 
	$Ahome["Tarticles_logo"] = $Aitem["two"]["articles"][4];
	$orderby = 'arti_date';$limits=$Aitem["two"]["articles"][1];$substr=30;
	$Ahome['Tarticles'] = articles_list($orderby, $limits,$acid='',$substr);
}else{
	$Ahome["Tarticles"] = false;
}
//置顶新闻
if($Aitem["two"]["articles_top"][0]){
	$Ahome["Tarticles_top_title"] = $Aitem["two"]["articles_top"][3]; 
	$Ahome["Tarticles_top_logo"] = $Aitem["two"]["articles_top"][4];
	$orderby = 'top';$limits=$Aitem["two"]["articles_top"][1];$substr=30;
	$Ahome['Tarticles_top'] = articles_list($orderby, $limits,$acid='',$substr); 
}else{
	$Ahome["Tarticles_top"] = false;
}
//焦点新闻
if($Aitem["two"]["articles_focus"][0]){
	$Ahome["Tarticles_focus_title"] = $Aitem["two"]["articles_focus"][3];
	$Ahome["Tarticles_focus_logo"] = $Aitem["two"]["articles_focus"][4];
	$orderby = 'focus';$limits=$Aitem["two"]["articles_focus"][1];$substr=30;
	$Ahome["Tarticles_focus"] = articles_list($orderby, $limits,$acid='',$substr); 	 
}else{
	$Ahome["Tarticles_focus"] = false;
}
//滚动新闻
if($Aitem["two"]["articles_trundle"][0]){
	$Ahome["Tarticles_trundle_title"] = $Aitem["two"]["articles_trundle"][3]; 
	$Ahome["Tarticles_trundle_logo"] = $Aitem["two"]["articles_trundle"][4];
	$orderby = 'trundle';$limits=$Aitem["two"]["articles_trundle"][1];$substr=30;
	$Ahome["Tarticles_trundle"] = articles_list($orderby, $limits,$acid='',$substr);	
}else{
	$Ahome["Tarticles_trundle"] = false;
}
//新闻图库
if($Aitem["two"]["articles_ifpic"][0]){
	$Ahome["Tarticles_ifpic_title"] = $Aitem["two"]["articles_ifpic"][3]; 
	$Ahome["Tarticles_ifpic_logo"] = $Aitem["two"]["articles_ifpic"][4];
	$orderby = 'ifpic';$limits=$Aitem["two"]["articles_ifpic"][1];$substr=30;
	$Ahome["Tarticles_ifpic"] = articles_list($orderby, $limits,$acid='',$substr,' a.ifpic=1');
}else{
	$Ahome["Tarticles_ifpic"] = false;
}
//评论最多的文章
if($Aitem["two"]["articles_comms"][0]){
	$Ahome["Tarticles_comms_title"] = $Aitem["two"]["articles_comms"][3]; 
	$Ahome["Tarticles_comms_logo"] = $Aitem["two"]["articles_comms"][4];
	$orderby = 'comms';$limits=$Aitem["two"]["articles_comms"][1];$substr=30;
	$Ahome["Tarticles_comms"] = articles_list($orderby, $limits,$acid='',$substr);
}else{
	$Ahome["Tarticles_comms"] = false;
}
//商品列表
if($Aitem["two"]["products"][0]){
	$Ahome["Tproducts_title"] = $Aitem["two"]["products"][3]; 
	$Ahome["Tproducts_logo"] = $Aitem["two"]["products"][4]; 
	$limit = $Aitem["two"]["products"][1]; 
	$orderby = 'prid desc ';$limits=$Aitem["two"]["products"][1];
	$Ahome['Tproducts'] = products_list($orderby, $limit); 
}else{
	$Ahome["Tproducts"] = false;
}
//特价商品
if($Aitem["two"]["products_top"][0]){
	$Ahome["Tproducts_top_title"] = $Aitem["two"]["products_top"][3];
	$Ahome["Tproducts_top_logo"] = $Aitem["two"]["products_top"][4];
	$limit = $Aitem["two"]["products_top"][1]; 
	$orderby = 'top desc ';$limits=$Aitem["two"]["products_top"][1];
	$Ahome['Tproducts_top'] = products_list($orderby, $limit);
}else{
	$Ahome["Tproducts_top"] = false;
}
//促销商品
if($Aitem["two"]["products_special"][0]){
	$Ahome["Tproducts_special_title"] = $Aitem["two"]["products_special"][3];
	$Ahome["Tproducts_special_logo"] = $Aitem["two"]["products_special"][4];
	$limit = $Aitem["two"]["products_special"][1]; 
	$orderby = 'special desc ';$limits=$Aitem["two"]["products_special"][1];
	$Ahome['Tproducts_special'] = products_list($orderby, $limit);
}else{
	$Ahome["Tproducts_special"] = false;
} 
//推荐品牌
if($Aitem["two"]["probrand"][0]){
	$Ahome["Tprobrand_title"] = $Aitem["two"]["probrand"][3];
	$Ahome["Tprobrand_logo"] = $Aitem["two"]["probrand"][4];
	$limit = $Aitem["two"]["probrand"][1]; 
	$sql   = "SELECT prbid,brand_name,brand_logo  FROM ".$pre."probrand 
		   WHERE `domain_id` =".$Aconf['domain_id']." AND is_show=1 
		   order by sort_order asc 
		   LIMIT ".$limit; 
    $row = $oPub->select($sql);
    if($row)
    foreach ($row AS $key=>$value) {
		$row[$key]["brand_logo"] = ($value["brand_logo"] == '')?'images/command/no_brandimgs.png':'data/brandlogo/'.$value["brand_logo"];	
		if($Aconf['rewrite']){
			$row[$key]['brand_url'] = 'brand-'.$value["prbid"].'-0.html'; 
		}else{
			$row[$key]['brand_url'] = 'brand.php?id='.$value["prbid"]; 
		} 	   
    }
	$Ahome["Tprobrand"] = $row; 
}else{
	$Ahome["Tprobrand"] = false;
}
//推荐经销商
if($Aitem["two"]["pravail"][0]){ 
	$Ahome["Tpravail_title"] = $Aitem["two"]["pravail"][3];
	$Ahome["Tpravail_logo"] = $Aitem["two"]["pravail"][4];
	$limit = $Aitem["two"]["pravail"][1]; 
    //$sql = "SELECT praid,pra_name,shop_logo  FROM ".$pre."pravail WHERE domain_id=".".$Aconf['domain_id']." ." AND ifshow=1 and cotype = 0 ORDER BY sort_order,praid ASC";
	$sql = "SELECT praid,pra_name,shop_logo  FROM ".$pre."pravail WHERE domain_id=".$Aconf['domain_id']."  and cotype = 0 ORDER BY sort_order,praid ASC";
    $row = $oPub->select($sql); 
	if($row)
    foreach ($row AS $key=>$val) {	
		$row[$key]["shop_logo"] = ($val["shop_logo"] == '')?'images/command/no_shoplogo.png':$val["shop_logo"];
		if($Aconf['rewrite']){
			$row[$key]['pravail_url'] = 'shop-'.$val["praid"].'.html'; 
		}else{
 			$row[$key]['pravail_url'] =  'shop.php?id='.$val["praid"];
		} 
	}
	$Ahome["Tpravail"] = $row;
}else{
	$Ahome["Tpravail"] = false;
}
 
//调查列表
if($Aitem["two"]["votes"][0]){
	$Ahome["Tvotes_title"] = $Aitem["two"]["votes"][3]; 
	$Ahome["Tvotes_logo"] = $Aitem["two"]["votes"][4];
	$limit = $Aitem["two"]["votes"][1];
	$where = "a.states=0 AND a.is_show = 1 AND a.domain_id =  ".$Aconf['domain_id'];
	$row = $oPub->select("SELECT a.vtid,a.vt_name,a.vt_desc,a.vt_nums,a.add_time FROM ".$pre."vote_title as a  WHERE  $where ORDER BY a.add_time DESC LIMIT ". $limit); 
	if($row ) { 
		foreach ($row AS $key=>$val) {
			$row[$key]['vt_desc'] = sub_str(clean_html($val["vt_desc"]),100,true);
			$row[$key]['add_time']  = ($val['add_time'])?date("m月d日h:i", $val['add_time']):'';
			if($Aconf['rewrite']){
				$row[$key]['vt_url'] = 'vote-'.$val["vtid"].'-0.html'; 
			}else{
				$row[$key]['vt_url'] =  'vote.php?id='.$val["vtid"];
			} 
		} 
		$Ahome["Tvotes"] = $row ;
	}
}else{
	$Ahome["Tvotes"] = false;
}
//关键词推荐
if($Aitem["two"]["keytj"][0]){
	$Ahome["keytj_title"] = $Aitem["two"]["keytj"][3]; 
	$Ahome["keytj_logo"] = $Aitem["two"]["keytj"][4];
	$limit = $Aitem["two"]["keytj"][1];  
	$row = $oPub->select('SELECT a.arid,a.keys,b.main_domin,b.header_title FROM '.$pre.'arti_tag as a,'.$pre.'sysconfig as b 
						  where a.domain_id=b.scid and a.domain_id= "'.$Aconf['domain_id'].'" order by a.top desc limit '.$limit);
	while( @list( $key, $val ) = @each( $row ) ) 
	{ 
		$row[$key]['article_url'] = 'http://'.$val['main_domin'].'/';
		if($Aconf['rewrite']){
			if($val['art_pro_type'] < 1)
			{
				$row[$key]['article_url'] .= 'article-'.$val['arid'].'-0.html';
			}else
			{
				$row[$key]['article_url'] .= 'product-'.$val['arid'].'.html';
			}
		}else{
			if($val['art_pro_type'] < 1)
			{
				$row[$key]['article_url'] .= 'article.php?id='.$val['arid'];
			}else
			{
				$row[$key]['article_url'] .= 'product.php?id='.$val['arid'];
			} 
		}  
	}

	$Ahome["keytj"] = $row; 
	unset($row);
}else{
	$Ahome["keytj"] = false;
} 
//友情链接
if($Aitem["two"]["links"][0]){
	$Ahome["links_title"] = $Aitem["two"]["links"][3];  
	$Ahome["links_logo"] = $Aitem["two"]["links"][4]; 
	$strWhere = ($Aconf['links'])?" is_show=1 AND ":"  ";
	$strWhere = " WHERE ".$strWhere."  domain_id= ".$Aconf['domain_id']  ;

    $sql = "SELECT lk_name,lk_logo,site_url  FROM ".$pre."links ".$strWhere."  
		    ORDER BY sort_order,lkid ASC limit ".$Aitem["two"]["links"][1];
    $Alinks = $oPub->select($sql);
    if($Alinks)
    foreach ($Alinks AS $key=>$value)
    {
       $Alinks[$key]["lk_logo"] = ($value["lk_logo"] == '')?false:'data/links/'.$value["lk_logo"];
    }
	$Ahome["links"] = $Alinks; 
}else{
	$Ahome["links"] = false;
}
//新注册会员
if($Aitem["two"]["users"][0]){
	$Ahome["Tusers_title"] = $Aitem["two"]["users"][3]; 
	$Ahome["Tusers_logo"] = $Aitem["two"]["users"][4];

	$strWhere = ($Aconf['users'])?" is_show=1 AND ":"  ";
	$strWhere = " WHERE ".$strWhere."  domain_id= ".$Aconf['domain_id']  ;

	$row = $oPub->select('SELECT id,user_name,reg_time,avatar FROM '.$pre.'users where domain_id= "'.$Aconf['domain_id'].'" order by id desc limit '.$Aitem["two"]["users"][1]); 
	while( @list( $k, $v ) = @each( $row ) ) { 
		$row[$k]['reg_time'] = date("Y年m月d日 H:i",$v['reg_time']);
		if($v['avatar'] > 0)
		{
			$row[$k]['avatar'] = 'data/userimg/avatar_small/'.$v['id'].'_small.jpg';
		}else
		{
			$row[$k]['avatar'] = 'images/command/osunt_back.png';
		}
	} 
	$Ahome['Tusers']=$row;
	unset($row);
}else
{
	$Ahome['Tusers']=false;
}

/* three  start */
/* 新闻多个分类的综合调用
    所有分类调用$home.acids
    单个分类调用$home.acids.$key (其中key值通过后台，模版管理->页面显示设置，新闻子分类确认)   
    单个分类标题调用 $home.acids_title.$key                                                                                           
*/
if(!isset($Aitem["three"])) $Aitem["three"]=false; 
if($Aitem["three"]){
    $Aacids = array();
    foreach ($Aitem["three"] AS $key=>$Av) {
        if($Av[0]){ 
            $Aacids[$key]["title"] = $Av[3];
			$Aacids[$key]["logo"] = $Av[4];
            $orderby = 'top';$limits=$Av[1];$substr=30;
			if(count(explode(",",$Av[2])) > 1){
				$Aacids[$key]["url"]   = 'articles.php?more='.$Av[2];
			}else{
				$Aacids[$key]["url"]   = 'articles.php?id='.$Av[2];
			}
			//得到子分类
			$tmpid  = next_node_all($Av[2],$pre."articat","acid",true); 
			$strID  = ($tmpid)?$Av[2].','.$tmpid:$Av[2]; 

            $Aacids[$key]["list"] = articles_list($orderby, $limits,$strID,$substr);  
        }else{
            $Aacids[$key] = false;
        }    
    }
    $Ahome["acids"] =  $Aacids;  
}

/* 最新新注册用户 */
$row= $oPub->getRow('SELECT user_name,reg_time FROM '.$pre.'users where domain_id= "'.$Aconf['domain_id'].'" order by id desc limit 1'); 
$row['reg_time'] = date("m月d日 H:i",$row['reg_time']);
$Ahome['users_one']=$row;
//新注册会员
$row = $oPub->select('SELECT id,user_name,reg_time,avatar FROM '.$pre.'users where domain_id= "'.$Aconf['domain_id'].'" order by id desc limit 16'); 
while( @list( $k, $v ) = @each( $row ) ) { 
	$row[$k]['reg_time'] = date("Y年m月d日 H:i",$v['reg_time']);
	if($v['avatar'] > 0)
	{
		$row[$k]['avatar'] = 'data/userimg/avatar_small/'.$v['id'].'_small.jpg';
	}else
	{
		$row[$k]['avatar'] = 'images/command/osunt_back.png';
	}
} 
$Ahome['users_index']=$row;
// 在线 访客
$Ahome['login_ok'] = $Ahome['login_no'] = 0;
if (!defined('INIT_NO_USERS'))
{
	$Ahome['login_ok'] = 155 +  $sess->get_users_count(true);
	$Ahome['login_no'] = 185 +$sess->get_users_count();
}

//在线访客列表 start
if($Aconf['allow_home'] == $_SESSION['user_id'])
{
	$row = $oPub->select('SELECT data,expiry FROM ' . $pre.'sessions where userid > 0 order by expiry desc');  
	while( @list( $k, $v ) = @each( $row ) ) 
	{
		$row[$k]["expiry"] = date("H:i",$v['expiry']);
		$Aexpiry = unserialize($v['data']); 
		$row[$k]["user_name"]   = $Aexpiry['user_name'];

	}
	$Ahome['lineusers']=$row;
	unset($row);
}else
{
	$Ahome['lineusers']=false;
}
//在线访客列表 end

//flash轮播广告 start  id,name,imgwidth,imgheight,limits,showtype
//显示方式 $Ashowtype = array(0=>'其它方式',1=>'FLAS轮播');此数组对应 /admin/tucat.php 10行,文字推荐 
$row = $oPub->select('SELECT * FROM '.$pre.'tjcat where showtype<1 and domain_id= "'.$Aconf['domain_id'].'" order by id asc');
$n = 0;
while( @list( $k, $v ) = @each( $row ) ) {
	$n ++ ;
	$limit  = $v["limits"]>0?$v["limits"]:6;
	$tjkey = "showli_".$n; 
	$Ahome["tj"][$tjkey] = false; 
	$sql = "SELECT * FROM  ".$pre."tj  where tjcatid=".$v["id"]." and domain_id= ".$Aconf['domain_id']."  order by orders limit ".$limit;
	$rowtjvalue = $oPub->select($sql); 
 	//显示方式，处理 start  
	while( @list($key, $val) = @each($rowtjvalue)){ 
		if($val["img"]){
			$rowtjvalue[$key]["img"] = 'data/tj/'.$val["img"];
		}else
		{
			$rowtjvalue[$key]["img"] = 'images/command/no_imgsbig.png';
		} 
	} 
	$Ahome["tj"][$tjkey] = $rowtjvalue;
	$Ahome["tj_title"][$tjkey] = $v['name'];
}
//flash 推荐部分
//flash 推荐部分
//显示方式 $Ashowtype = array(0=>'其它方式',1=>'FLAS轮播',2=>'优酷视频');此数组对应 /admin/tjcat.php 10行,文字推荐
$row = $oPub->select('SELECT * FROM '.$pre.'tjcat where  domain_id= "'.$Aconf['domain_id'].'" order by orders asc'); 
while( @list( $k, $v ) = @each( $row ) ) {  
	$limit  = $v["limits"]>0?$v["limits"]:6;
	if($v['showtype'] == 1)
	{
		$tjkey = "show_".$v['orders']; 
	}elseif($v['showtype'] == 2)
	{
		$tjkey = "showyk_".$v['orders'];  
	}else
	{
		$tjkey = "showli_".$v['orders'];
	}
	$Ahome["tj"][$tjkey] = false; 
	$rowtjvalue = $oPub->select("SELECT * FROM  ".$pre."tj  where tjcatid=".$v["id"]." and domain_id= ".$Aconf['domain_id']."  order by orders limit ".$limit); 
	$picxs = $links = $texts = '';
	if($v['showtype'] < 1 )
	{
		while( @list($key, $val) = @each($rowtjvalue)){ 
			if($val["img"]){
				$rowtjvalue[$key]["img"] = 'data/tj/'.$val["img"];
			}else{
				$rowtjvalue[$key]["img"] = 'images/command/no_imgsbig.png';
			} 
		} 
		$Ahome["tj"][$tjkey] = $rowtjvalue;
	}

	if($v['showtype'] == 1 )
	{
		while( @list($key, $val) = @each($rowtjvalue)){ 
			if($val["img"]){
				$rowtjvalue[$key]["img"] = 'data/tj/'.$val["img"];
			}else{
				$rowtjvalue[$key]["img"] = 'images/command/no_imgsbig.png';
			}
		   $picxs .= $rowtjvalue[$key]["img"].'|';
		   $links .=  str_replace("&"," ",$val["url"]).'|';
		   $texts .= $val["name"].'|';			
		} 
		//flash
		if(!empty($picxs)) 
		{ 
			$picxs     = substr($picxs,0,-1);
			$links     = substr($links,0,-1);
			$texts    = substr($texts,0,-1); 
			
			$Ahome["tj"][$tjkey] = '<script type="text/javascript">
					//<![CDATA[
					 var focus_width='.$v["imgwidth"].';
					 var focus_height='.$v["imgheight"].';
					 var text_height=0;
					 var swf_height=focus_height+text_height;
					 var pics="'.$picxs.'";
					 var links="'.$links.'";
					 var texts="'.$texts.'";
					 document.write(\'<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="\'+ focus_width +\'" height="\'+ swf_height +\'">\');
					 document.write(\'<param name="allowScriptAccess" value="sameDomain"><param name="movie" value="data/picviewer.swf"><param name="quality" value="high"><param name="bgcolor" value="#F0F0F0">\');
					 document.write(\'<param name="menu" value="false"><param name=wmode value="opaque">\');
					 document.write(\'<param name="FlashVars" value="pics=\'+pics+\'&links=\'+links+\'&texts=\'+texts+\'&borderwidth=\'+focus_width+\'&borderheight=\'+focus_height+\'&textheight=\'+text_height+\'">\');
					 document.write(\'<embed src="data/picviewer.swf" wmode="opaque" FlashVars="pics=\'+pics+\'&links=\'+links+\'&texts=\'+texts+\'&borderwidth=\'+focus_width+\'&borderheight=\'+focus_height+\'&textheight=\'+text_height+\'" menu="false" bgcolor="#F0F0F0" quality="high" width="\'+ focus_width +\'" height="\'+ focus_height +\'" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />\');  document.write(\'</object>\');
					//]]>
					</script>';   
		} 
	}
	//优酷
	if($v['showtype'] == 2 )
	{
		//http://v.youku.com/v_show/id_XNDg0MjQzNTMy.html 
		while( @list($key, $val) = @each($rowtjvalue)){ 
			if(!empty($val['url']))
			{ 
				$sid = preg_replace(array("'http://v.youku.com/v_show/id_'","'.html'"),array("",""),$val['url']);  
				$Ahome["tj"][$tjkey] = '<EMBED  width="'.$v["imgwidth"].'"  height="'.$v["imgheight"].'" type="application/x-shockwave-flash" align="middle"src="http://player.youku.com/player.php/sid/'.$sid.'/v.swf" allowFullScreen="true" quality="high" allowScriptAccess="always"></EMBED>';
 			}else
			{
				$Ahome["tj"][$tjkey] = false;
			} 
			break;
		} 

	}

	$Ahome["tj_title"][$tjkey] = $v['name'];
} 
 //直接注册的下级网站代理 
$sql = "SELECT count(*) as count FROM ".$pre."sysconfig where pre_scid= ".$Aconf['domain_id'] ; 
$count = $oPub->getOne($sql);  
if($count > 0 ){
	$sql = "SELECT scid,main_domin,header_title FROM ".$pre."sysconfig where  pre_scid= ".$Aconf['domain_id']." order by scid desc"; 
	$row = $oPub->select($sql); 
	while( @list( $k, $v ) = @each( $row) ) {  
		$row[$k]["main_domin"]   =  'http://'.$v['main_domin'].'/'.$SUBPATH;
		$row[$k]["header_title"] = $v['header_title'];
		$add_time= $oPub->getOne("SELECT add_time FROM ".$pre."admin_user  where domain_id=".$v["scid"]." order by user_id asc limit 1");
		if($add_time) 
		{
			$row[$k]["add_time"] = date("y年m月d",$add_time); 
		}else{
			$row[$k]["add_time"] =  '';
		}
	}
	$Ahome["pre_web"] = $row;
}
$Ahome["pre_web_count"] = $count;

/* 推荐热词 总站使用 start */ 
if($Aconf['allow_home'] == $Aconf['domain_id'])
{ 
	$row = $oPub->select('SELECT a.arid,a.keys,b.main_domin,b.header_title FROM '.$pre.'arti_tag as a,'.$pre.'sysconfig as b 
						  where a.domain_id=b.scid order by a.top desc limit 10');
	while( @list( $key, $val ) = @each( $row ) ) 
	{ 
		$row[$key]['article_url'] = 'http://'.$val['main_domin'].'/';
		if($Aconf['rewrite']){
			if($val['art_pro_type'] < 1)
			{
				$row[$key]['article_url'] .= 'article-'.$val['arid'].'-0.html';
			}else
			{
				$row[$key]['article_url'] .= 'product-'.$val['arid'].'.html';
			}
		}else{
			if($val['art_pro_type'] < 1)
			{
				$row[$key]['article_url'] .= 'article.php?id='.$val['arid'];
			}else
			{
				$row[$key]['article_url'] .= 'product.php?id='.$val['arid'];
			} 
		}  
	}
	$Ahome['tag_title'] = '热词推荐：';
	$Ahome['tag'] = $row; 
}
/* 推荐热词 总站使用 end */
//导航条
$Ahome["nav"] = get_nav(); 
$Ahome["nav_botton"] = get_nav(1); 
$Ahome["nav_help"] = get_nav(2);

?>