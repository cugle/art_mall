// JavaScript Document
function shutwin(){
  window.close();
  return;
}
function xy_selectall()
{
  xx = form_1.check_all.checked
  for(var i=0;i<form_1.length;i++)
  {
    if(form_1.elements[i].type=="checkbox")
      form_1.elements[i].checked=xx;
  }
}