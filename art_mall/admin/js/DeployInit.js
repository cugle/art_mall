flag=1;
function ClearAdminDeploy(){
	
	var deployitem=FetchCookie("deploy");
	var admin_start;
	var userdeploy='';
	admin_start= deployitem ? deployitem.indexOf("\n") : -1;
	if(admin_start!=-1){
		userdeploy=deployitem.substring(0,admin_start);
	}
	//alert(document.getElementById('flag'));
	for(i=0;i<20;i++){
		obj=document.getElementById("cate_"+"b"+i);	
		img=document.getElementById("img_"+"b"+i);
		if(obj && obj.style.display=="none"){
			obj.style.display="";
			img_re=new RegExp("_open\\.gif$");
			img.src=img.src.replace(img_re,'_fold.gif');
		}
	}
	deployitem=userdeploy+"\n\t\t";
	SetCookie("deploy",deployitem);
	//flag=0;
}

function SetAdminDeploy(){
	var deployitem=FetchCookie("deploy");
	var admin_start;
	var userdeploy='';
	var admindeploy='';
	var i;
	admin_start= deployitem ? deployitem.indexOf("\n") : -1;
	if(admin_start!=-1){
		userdeploy=deployitem.substring(0,admin_start);
	}
	for(i=0;i<20;i++){
		obj=document.getElementById("cate_"+"b"+i);	
		img=document.getElementById("img_"+"b"+i);
		if(obj && obj.style.display==""){
			obj.style.display="none";
			img_re=new RegExp("_fold\\.gif$");
			img.src=img.src.replace(img_re,'_open.gif');
		}
		admindeploy=admindeploy+"b"+i+"\t";
	}
	deployitem=userdeploy+"\n\t"+admindeploy;
	SetCookie("deploy",deployitem);
	//flag=1;
}
function deploy(){
alert('ok');
}