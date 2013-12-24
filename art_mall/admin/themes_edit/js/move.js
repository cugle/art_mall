var dragobj={}
window.onerror=function(){return false}
var domid=12
function on_ini(){
	String.prototype.inc=function(s){return this.indexOf(s)>-1?true:false}
	var agent=navigator.userAgent
	window.isOpr=agent.inc("Opera")
	window.isIE=agent.inc("IE") && !isOpr
	window.isMoz=agent.inc("Mozilla") && !isOpr && !isIE
	if(isMoz){
	Event.prototype.__defineGetter__("x",function(){return this.clientX+2})
	Event.prototype.__defineGetter__("y",function(){return this.clientY+2})
	}
	basic_ini()
}
function basic_ini(){
	window.$=function(obj){return typeof(obj)=="string"?document.getElementById(obj):obj}
	window.oDel=function(obj){if($(obj)!=null){$(obj).parentNode.removeChild($(obj))}}
}
window.oDel=function(obj){if($(obj)!=null){$(obj).parentNode.removeChild($(obj))}}
window.onload=function(){
	on_ini()
	var o=document.getElementsByTagName("h1")
	for(var i=0;i<o.length;i++){
		o[i].onmousedown=addevent;
		//添加折叠和关闭按钮
		var tt = document.createElement("div");
		tt.style.cssText = "float:left";
		var span = document.createElement("span");
		span.innerHTML = "▽"+o[i].innerHTML;
		span.style.cssText = "cursor:default;";
		span.onmousedown = minusDiv;
		tt.appendChild(span);
		var close = document.createElement("div");
		close.innerHTML = "×";
		close.style.cssText = "cursor:default;float:right";
		close.onmousedown = closeDiv;
		o[i].innerHTML = "";
		o[i].appendChild(tt);
		o[i].appendChild(close);
	}
}
//折叠或者显示层
function minusDiv(e)
{
	e=e||event
	var nr = this.parentNode.parentNode.nextSibling;    //取得内容层
	nr.style.display = nr.style.display==""?"none":"";
}
//移出层
function closeDiv(e)
{
	e=e||event
	var mdiv = this.parentNode.parentNode;    //取得目标层
	oDel(mdiv);
}
function addevent(e){
	if(dragobj.o!=null)
	return false
	e=e||event
	dragobj.o=this.parentNode
	dragobj.xy=getxy(dragobj.o)
	dragobj.xx=new Array((e.x-dragobj.xy[1]),(e.y-dragobj.xy[0]))
	//dragobj.o.className = 'dragging';
	dragobj.o.style.width=dragobj.xy[2]+"px"
	dragobj.o.style.height=dragobj.xy[3]+"px"
	dragobj.o.style.left=(e.x-dragobj.xx[0])+"px"
	dragobj.o.style.top=(e.y-dragobj.xx[1])+"px"
	dragobj.o.style.position="absolute"
	dragobj.o.style.filter='alpha(opacity=60)';        //添加拖动透明效果
	var om=document.createElement("div")
	dragobj.otemp=om
	om.style.width=dragobj.xy[2]+"px"
	om.style.height=dragobj.xy[3]+"px"
	om.style.border = "1px dashed red";    //ikaiser添加，实现虚线框
	dragobj.o.parentNode.insertBefore(om,dragobj.o)
	return false
}

document.onselectstart=function(){return false}
window.onfocus=function(){document.onmouseup()}
window.onblur=function(){document.onmouseup()}
document.onmouseup=function(){
	if(dragobj.o!=null){
		dragobj.o.style.width="auto"
		dragobj.o.style.height="auto"
		dragobj.otemp.parentNode.insertBefore(dragobj.o,dragobj.otemp)
		dragobj.o.style.position=""
		oDel(dragobj.otemp)
		dragobj={}
	}
}
document.onmousemove=function(e){
	e=e||event
	if(dragobj.o!=null){
		dragobj.o.style.left=(e.x-dragobj.xx[0])+"px"
		dragobj.o.style.top=(e.y-dragobj.xx[1])+"px"
		createtmpl(e, dragobj.o)    //传递当前拖动对象
	}
}
function getxy(e){
	var a=new Array()
	var t=e.offsetTop;
	var l=e.offsetLeft;
	var w=e.offsetWidth;
	var h=e.offsetHeight;
	while(e=e.offsetParent){
		t+=e.offsetTop;
		l+=e.offsetLeft;
	}
	a[0]=t;a[1]=l;a[2]=w;a[3]=h
	return a;
}

function inner(o,e){
	var a=getxy(o)
	if(e.x>a[1] && e.x<(a[1]+a[2]) && e.y>a[0] && e.y<(a[0]+a[3])){
		if(e.y<(a[0]+a[3]/2))
			return 1;
		else
			return 2;
		}else
			return 0;
}

//将当前拖动层在拖动时可变化大小，预览效果
function createtmpl(e, elm){
	for(var i=0;i<domid;i++){
	if(document.getElementById("m"+i) == null)    //已经移出的层不再遍历
	continue;
	if($("m"+i)==dragobj.o)
	continue
	var b=inner($("m"+i),e)
	if(b==0)
	continue
	dragobj.otemp.style.width=$("m"+i).offsetWidth
	elm.style.width = $("m"+i).offsetWidth;
	//1为下移，2为上移
	if(b==1){
	$("m"+i).parentNode.insertBefore(dragobj.otemp,$("m"+i))
	}else{
	if($("m"+i).nextSibling==null){
	$("m"+i).parentNode.appendChild(dragobj.otemp)
	}else{
	$("m"+i).parentNode.insertBefore(dragobj.otemp,$("m"+i).nextSibling)
	}
	}
	return
	}
	for(var j=0;j<3;j++){
	if($("dom"+j).innerHTML.inc("div")||$("dom"+j).innerHTML.inc("DIV"))
	continue
	var op=getxy($("dom"+j))
	if(e.x>(op[1]+10) && e.x<(op[1]+op[2]-10)){
	$("dom"+j).appendChild(dragobj.otemp)
	dragobj.otemp.style.width=(op[2]-10)+"px"
	}
	}
}

function add_div()
{
	var o=document.createElement("div")
	o.className="mo"
	o.id="m"+domid
	$('dom0').appendChild(o)
	o.innerHTML="<h1>dom"+domid+"</h1><div class=nr></div>"
	o.getElementsByTagName("h1")[0].onmousedown=addevent
	domid++
}