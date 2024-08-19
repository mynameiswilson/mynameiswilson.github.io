function windowOnLoad(f) { var prev=window.onload; window.onload=function(){ if(prev)prev(); f(); }}
function d2h(d) {var h = hD.substr(d&15,1);	while(d>15) {d>>=4;h=hD.substr(d&15,1)+h;}return h;}
function h2d(h) {return parseInt(h,16);}
function round(number,X) {X = (!X ? 2 : X); return Math.round(number*Math.pow(10,X))/Math.pow(10,X);}
function getHexColor(r,g,b) {return eval("'rgb(" + r + "," + g + "," + b + ")'.parseColor()");}

