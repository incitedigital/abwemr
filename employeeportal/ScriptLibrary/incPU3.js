/*
 Pure ASP Upload 3
 Version: 3.1.1
 (c) 2012 DMXzone.com
 @build 13-12-2012 14:00:04
*/
var dmxUploadIsAjax=!1;
function validateForm(a,c,e){var d=window.event||arguments.callee.caller.arguments[0];if(dmxIsDefaultEventPrevented(d)||!1===document.MM_returnValue)return!1;var i=!0;if(a&&a.elements){for(var g=0;g<a.elements.length;g++){var k=a.elements[g],j=!1;if(k.type&&"file"==k.type.toLowerCase()){for(var b=3;b<arguments.length;b++)k.name&&k.name.toLowerCase()==arguments[b][0].toLowerCase()&&(validateFile(k,arguments[b][1],arguments[b][2]),j=!0);j||validateFile(k,c,e);if(!k.uploadOK){i=!1;break}}}i||dmxPreventDefaultEvent(d)}}
function validateFile(a,c,e){var d=a.value.replace(/"/gi,"");a.uploadOK=!1;""==d&&e?(alert(getLang(PU3_ERR_REQUIRED)),a.focus()):""!=c&&""!=d?checkExtension(a,d,c):a.uploadOK=!0}function checkExtension(a,c,e){var d=RegExp("\\.("+e.replace(/,/gi,"|").replace(/\s/gi,"")+")$","i");a.uploadOK=!1;d.test(c)?a.uploadOK=!0:(alert(getLang(PU3_ERR_EXTENSION,e)),a.focus())}function getLang(a){for(var c=a,e=1;e<arguments.length;e++)c=c.replace("%"+e,arguments[e]);return c}
function showProgressWindow(a,c,e,d){var i=!1,g=window.event||arguments.callee.caller.arguments[0],k=dmxIsDefaultEventPrevented(g)||!1===document.MM_returnValue;if(d)for(var j=0;j<d.elements.length;j++)field=d.elements[j],field.type&&"file"==field.type.toLowerCase()&&field.value&&""!=field.value&&(i=!0);else i=!0;if(!k&&i){if(window.jQuery&&jQuery.fn.ajaxForm&&(i=jQuery(d).data("events"))&&i.submit)for(j=0;j<i.submit.length;j++)if("form-plugin"==i.submit[j].namespace){if(void 0!==jQuery("<input type='file'/>").get(0).files&&
void 0!==window.FormData&&(0<jQuery("input:file:enabled[value]",d).length||"multipart/form-data"==jQuery(d).attr("enctype")||"multipart/form-data"==jQuery(d).attr("encoding")))dmxUploadIsAjax=!0,a=a.replace(/[&\?]?url=[^&]*|[&\?]?uploadid=[^&]*/gi,"");jQuery(d).bind("ajaxComplete",function(){b.close()})}var b=new progressPopup(a,c,e,d);window.onunload=function(){b.close()};window.opera&&!dmxUploadIsAjax&&dmxPreventDefaultEvent(g)}}
progressPopup=function(a,c,e,d){var i=this,g=document,k=!1;if(g.progressWindow)return c=g.progressWindow.object,c.loadUrl(a),c.bringToFront(),c;var j=g.createElement("div"),b=j.style;b.position="fixed";b.left="0";b.top="0";b.width="100%";b.height="100%";b.zIndex=9999;b.background="#000";"undefined"!=typeof b.opacity?b.opacity=0.3:"object"==typeof b.filters?b.filters.alpha.opacity=30:"string"==typeof b.filter&&(b.filter="alpha(opacity=30)");var l=g.createElement("div"),h=l.style;l.object=this;h.position=
"absolute";h.left="50%";h.top="50%";h.width=c+"px";h.height=e+"px";h.marginLeft=-c/2+"px";h.marginTop=-e/2+"px";h.zIndex=1E4;h.background="#fff";h.border="1px solid #999";h.WebkitBoxShadow="0 2px 5px rgba(0,0,0,.3)";h.MozBoxShadow="0 2px 5px rgba(0,0,0,.3)";h.boxShadow="0 2px 5px rgba(0,0,0,.3)";var f=g.createElement("iframe");f.src="about:blank";f.marginHeight="0";f.marginWidth="0";f.width=c;f.height=e;f.scrolling="no";f.border="0";f.frameborder="0";f.style.border="0px none";f.style.padding="0";
f.style.background="#fff";g.body.appendChild(j);l.appendChild(f);g.body.appendChild(l);g.progressWindow=l;this.loadUrl=function(a){f.src=a};this.bringToFront=function(){h.display="";b.display=""};this.close=function(){h.display="none";b.display="none";f.src="about:blank"};this.submit=function(){if(!k){k=true;d.submit()}};setTimeout(function(){if(window.opera&&!dmxUploadIsAjax){setTimeout(i.submit,1E3);f.onload=i.submit}f.src=a},200)};
function dmxIsDefaultEventPrevented(a){return a&&(a.defaultPrevented||!1===a.returnValue||a.getPreventDefault&&a.getPreventDefault())?!0:!1}function dmxPreventDefaultEvent(a){a&&(a.preventDefault&&(a.preventDefault(),a.defaultPrevented=!0),a.returnValue=!1)};