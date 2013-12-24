lastScrollY=0; 
function heartBeat(){ 
var scrollPos; 
if (typeof window.pageYOffset != 'undefined') { 
   scrollPos = window.pageYOffset; 
} 
else if (typeof document.compatMode != 'undefined' && 
     document.compatMode != 'BackCompat') { 
   scrollPos = document.documentElement.scrollTop; 
} 
else if (typeof document.body != 'undefined') { 
   scrollPos = document.body.scrollTop; 
} 

diffY=scrollPos;
percent=.1*(diffY-lastScrollY); 
if(percent>0)percent=Math.ceil(percent); 
else percent=Math.floor(percent); 
document.all.backi.style.pixelTop+=percent; 
lastScrollY=lastScrollY+percent; 
} 
window.setInterval("heartBeat()",1); 

function closeqq(){ 
	document.getElementById("backi").style.display = "none";
}


