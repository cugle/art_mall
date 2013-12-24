var http_request = false;
var obj;
function send_request(url) {
	http_request = false;
	if(window.XMLHttpRequest) { //Mozilla 
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	}else if (window.ActiveXObject) { // IE
	  try {
		http_request = new ActiveXObject("Msxml2.XMLHTTP");
	  } catch (e) {
		try {
			http_request = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {}
	 }
   }
   if (!http_request) { 
      document.getElementById(obj).innerHTML="";
      return false;
   }
	document.getElementById(obj).innerHTML="";
	http_request.onreadystatechange = processRequest;
	http_request.open("get", url, true);
	http_request.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	http_request.send(null);
}
	
function processRequest() {
   if (http_request.readyState == 4) { 
     if (http_request.status == 200) { 
		document.getElementById(obj).innerHTML=http_request.responseText;
     } else { 
	   //document.getElementById(obj).innerHTML="载入信息错误";
       alert("载入信息错误。");
	   //close loading
     }
  }
}

function selectsAjax(a,b,c,d,e) {  
   // a 选择的值   b :数据库名 c:操作类型del show install edit   d:显示出来的前缀 e:当前样式的后缀，数字自动清除当前样式 
   if( e )  {
		var i=0
		for (i=e;i<=5;i++) {
			var xytmp = d + "_"+i;  
			document.getElementById( xytmp ).innerHTML="";
		} 
		obj = d + "_"+e; 
		var strTemp = "selectsajax.php?op=" + c + "&cstyle=" + d + "&cstyleend="+ e +"&bdatebase=" + b + "&avalue=" + escape(a);
 
	}else{
		obj = d
		var strTemp = "selectsajax.php?op=" + c + "&cstyle=" + d + "&bdatebase=" + b + "&avalue=" + escape(a);
	}  
	send_request(strTemp);
}  

function ajaxLogin(){ 
	
	var topuser_name    = document.getElementById("topuser_name").value; 
	var toppassword     = document.getElementById("toppassword").value; 
	var msg = '';
	var reg = null;
 
	if(topuser_name.length < 2 || toppassword.length > 25 ){
		msg += '帐号错误' + '\n';
	}

 	if(toppassword.length < 1 ){
		msg += '密码错误' + '\n';
	}
	
	if (msg.length > 0){
		alert(msg);
		return false;
	}else
	{
		obj = 'toplogin';
		var strTemp = "user.php?o=al&topuser_name=" + escape( topuser_name )  + "&toppassword=" + escape( toppassword );  
		send_request(strTemp); 
	}
}

function cars_edit(op,prid,nums,shop_number)
{
	obj = "jies_botton";  
	prid = parseInt(prid);
	nums = parseInt(nums);
	shop_number = parseInt(shop_number);
	if(nums > shop_number){
		alert("超过库存数量");
	}else{
		if(nums > 0)
		{
			document.getElementById("jies_top").style.display   ='block';
			document.getElementById("jies_botton").style.display='block'; 
			var strTemp = "ajax_cars.php?op=" + op + "&prid=" + prid + "&nums=" + nums;  
			send_request(strTemp);
		} 
	}
	
}  