/*! For license information please see script.js.LICENSE.txt */
window.addEventListener("load",(function(){const e=window.koko_analytics;if(void 0===e)return void console.log("Koko Analytics: no configuration object found in DOM.");const o=String(e.post_id);!function(){if("doNotTrack"in navigator&&"1"===navigator.doNotTrack&&e.honor_dnt)return;if("visibilityState"in document&&"prerender"===document.visibilityState)return;if(/bot|crawler|spider|crawling|seo|chrome-lighthouse|preview/i.test(navigator.userAgent))return;if(window.location!==window.parent.location)return;const n=e.use_cookie?function(e){if(!document.cookie)return"";const o=document.cookie.split("; ");let n;for(let e=0;e<o.length;e++)if(n=o[e].split("="),"_koko_analytics_pages_viewed"===n[0])return decodeURIComponent(n[1]);return""}():"",t=n.split(",").filter((function(e){return""!==e}));let i=0===n.length,r=-1===t.indexOf(o),c="";"string"==typeof document.referrer&&""!==document.referrer&&(0===document.referrer.indexOf(window.location.origin)?(i=!1,document.referrer===window.location.href&&(r=!1)):c=document.referrer);const d=document.createElement("img");d.style.display="none",d.onload=function(){if(document.body.removeChild(d),e.use_cookie){-1===t.indexOf(o)&&t.push(o);const n=new Date;n.setHours(n.getHours()+6),function(o,n,t){let i=(o=window.encodeURIComponent(o))+"="+(n=window.encodeURIComponent(String(n)));i+=";path="+e.cookie_path+";SameSite=Lax;expires="+t.toUTCString(),document.cookie=i}("_koko_analytics_pages_viewed",t.join(","),n)}};let a="";a+="p="+o,a+="&nv="+(i?"1":"0"),a+="&up="+(r?"1":"0"),a+="&r="+encodeURIComponent(c),a+="&rqp="+Math.random().toString(36).substr(2),d.src=e.tracker_url+(e.tracker_url.indexOf("?")>-1?"&":"?")+a,document.body.appendChild(d)}()}));