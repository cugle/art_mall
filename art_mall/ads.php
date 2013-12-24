<?php 
$db_table = $pre."ad_position";
$strWhere = ""; 
//$Amedia_type = array(1=>'图片',2=>'Flash',3=>'代码',4=>'文字');
$Aads = array();$n   = 0;
$sql = "SELECT position_id,ad_width,ad_height,type  FROM ".$db_table.$strWhere." ORDER BY position_id asc ";
$row = $oPub->select($sql); 
if($row) {
	foreach($row AS $k => $v ) { 
	  $adkey           = 'ads_'.$v["ad_width"].'_'.$v["ad_height"].'_'.$v["position_id"];
	  $adkey_type      = 'type_ads_'.$v["ad_width"].'_'.$v["ad_height"].'_'.$v["position_id"];
	  $db_table        = $pre."ad";

	  $strWhere = " WHERE start_time < '".gmtime()."' and end_time > '".gmtime()."' and position_id ='".$v["position_id"]."' AND enabled = 1 ";
	  $strWhere .=  ' and domain_id='.$Aconf['domain_id']; 
      $sql = "SELECT *  FROM ".$db_table.$strWhere." ORDER BY ad_id  desc limit 1";
	  $rowad = $oPub->select($sql); 
	  if($rowad) {  
		  $media_type = $rowad[1]["media_type"];
		  $str = '';
          switch ($media_type) {
           case 2: 
			      $j = 0;
                  foreach($rowad AS $key => $vale ) {
						$j ++ ; 
						if(strstr($vale["ad_code"],'http://' )) {
							$ad_codetmp = $vale["ad_code"];
						} else {        
							$ad_codetmp = 'data/abcde/'.$vale["ad_code"];
						}

						$str .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="'.$v["ad_width"].'" height="'.$v["ad_height"].'">';
						$str .= '<param name="movie" value="'. $ad_codetmp.'" />';
						$str .= '<param name="quality" value="high" />';
						$str .= '<embed src="'. $ad_codetmp.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$v["ad_width"].'" height="'.$v["ad_height"].'"></embed>';
						$str .= '</object>';
						if($j >= 1) 
						  break;
				  }
				  $Aads[$adkey] = $str;
                  break;
           case 3:
			      $j ++ ;
                  foreach($rowad AS $key => $vale ) {
					  $str .= $vale["ad_code"];
					  if($j >= 1) 
						  break;
				  }
				  $Aads[$adkey] = $str;	 
                  break;
           case 4:
  			      $j = 0;
                  foreach($rowad AS $key => $vale ) {
					  $j ++ ;
					  $str .= '<A HREF="'.$vale["ad_link"].'" target="_blank">';
                      $str .= $vale["ad_link"];
                      $str .= '</A><br/>';
					  if($j >= 1) 
						  break;
				  }
				  $Aads[$adkey] = $str;
                  break;
           default:
			      $j = 0;
                  foreach($rowad AS $key => $vale ) {
					  $j ++ ;
					  $str .= '<A HREF="'.$vale["ad_link"].'"  target="_blank">';
                      $str .= '<IMG SRC="data/abcde/'.$vale["ad_code"].'" width="'.$v["ad_width"].'" HEIGHT="'.$v["ad_height"].'" BORDER="0" TITLE="'.$vale["ad_link"].'">';
                      $str .= '</A>';
					  if($j >= 1) 
						  break;
				  }
				  $Aads[$adkey] = $str;
                  break;
         } 
	  }
	  //$master_ads = 'master_ads_'.$n;
	  if(!isset($Aads["$adkey"])) 
		  $Aads["$adkey"]=false; 

	  //$smarty->assign($adkey,  $Aads["$adkey"] );  
  } //foreach
}

 
/* 广告  
$smarty->assign('ads_780_60_1',  $Aads[ads_780_60_1] ); 
*/