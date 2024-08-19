<!--//

  /*****

  snow2.js, version 1.0, 2002-12-11
  ben@thelocust.org 
  http://thelocust.org
  
  adapted from http://gobi.gmxhome.de/snow/
  but reconfigured a bit, works in IE6 and NS7, Mozilla 1.2.1 

  CHANGELOG

  2002-12-11
  * added a working startstopsnow function, which will (as you guessed) start and stop the snow, making it appear/disappear.
  * added setupSnow
  * added resetSnow
  * added bStartImmediately
  * added bExplode

  2002-12-06
  *  now, if you click on the snowflakes, they explode.

  2002-12-05
  *  removed all that crap code for NS4, etc.  Found that most of the
  -  IE4 code works well in NS6/7, Mozilla, etc. 
 
  *  added "pauseSnow" function, which allows you to pause/unpause the snowflakes
  -  just make a link like <a href="javascript:pauseSnow()">pause</a> in yer HTML
  -  works like a charm!  


  ******/

// STUFF YOU CAN FOOL WITH
var no = 10;                   // number of snowflakes
var speed = 50;                // the smaler, the faster snowflakes
var bStartImmediately = 0;
var imgSnowflake = "files/flake.gif";    // picture source
var imgExplode = "files/explode.gif";    // picture source
var bExplode = 1; //0 to NOT have the flakes explode on click, or 1 to have them explode!

// STUFF YOU SHANT WORRY ABOUT UNLESS YOU KNOW WHAT YOU ARE DOING, 
// AND EVEN THEN YOU MIGHT BREAK IT. 
var tmpspeed = speed;                // DO NOT CHANGE THIS
var bHasFallen = 0;

var b4up = (document.body.clientWidth) ? 1 : 0;      // MSIE4, Opera5, Netccape5
var dx, xp, yp;                // coordinate and position variables
var am, stx, sty;              // amplitude and step variables
var snowobj;
var i, doc_width = 1024, doc_height = 768;
var bPaused = 0;
var bStopped = 0;
var bSetup = 0;

doc_width  = document.body.clientWidth;
doc_height = document.body.clientHeight;


dx = new Array();
xp = new Array();
yp = new Array();
am = new Array();
stx = new Array();
sty = new Array();
snowobj = new Array();
expTimer = new Array();

//do initial snowflake setup
function setupSnow() {

  for (i=0; i<no; ++i) {         // iterate for every snowflake
   dx[i] = 0;                  // set coordinate variables
   xp[i] = Math.random()*(doc_width-50); // set position variables
   yp[i] = Math.random()*doc_height;
   am[i] = Math.random()*20;             // set amplitude variables
   stx[i] = 0.02 + Math.random()/10;     // set step variables
   sty[i] = 0.7 + Math.random();         // set step variables
   if (b4up) {
      document.write("<DIV ID=\"flake"+ i +"\" onclick=\"javascript:destructFlake("+ i +");\" STYLE=\""
      + "position:absolute; z-index:"+ i +"; visibility:visible; "
      + "top:15px; left:15px;\"><IMG ID=\"imgflake" + i + "\" SRC=\""+ imgSnowflake
      + "\" BORDER=0></DIV>");

      snowobj[i] = eval (document.getElementById("flake"+i).style);
   }//if
  }//for
 
  bSetup = 1;
  resetSnow();

} //function

//set all snow to above the top of the page.
function resetSnow() {
  for (i=0; i<no; ++i) {         // iterate for every snowflake
   dx[i] = 0;                  // set coordinate variables
   xp[i] = Math.random()*(doc_width-50); // set position variables
   yp[i] = Math.random()*doc_height;
   am[i] = Math.random()*20;             // set amplitude variables
   stx[i] = 0.02 + Math.random()/10;     // set step variables
   sty[i] = 0.7 + Math.random();         // set step variables
  
   snowobj[i].top= -50
  } // for

}


function snowDocument() {      // MSIE4, Opera5, Netscape5 main
   if (bSetup == 0) { setupSnow(); }

   if (bPaused == 0) {
   for (i=0; i<no; ++i) {      // iterate for every flake
      yp[i] += sty[i];
      if (yp[i] > doc_height-50) {
         bHasFallen = 1;
         xp[i] = Math.random()*(doc_width-am[i]-30);
         yp[i] = -20;
         stx[i] = 0.02 + Math.random()/10;
         sty[i] = 0.7 + Math.random();
      }//if
      else { bHasFallen = 0; }

      dx[i] += stx[i];
      snowobj[i].top  = yp[i];
      snowobj[i].left = xp[i] + am[i]*Math.sin(dx[i]);

      if (document.getElementById('imgflake' + i).src == imgExplode) { 
        if (isNaN(expTimer[i])) { expTimer[i] = 0; }
	//alert(expTimer[i]);
        expTimer[i] += speed;
        if (expTimer[i] > 500) { 
		yp[i] = doc_height-50;        
		snowobj[i].top = doc_height-50;        
		snowobj[i].left = -50;        
		document.getElementById('imgflake' + i).src = imgSnowflake;
                expTimer[i] = 0;
          } //if the explosion timer is ready to go off
        }//if exploding

      
   }//for
    
   setTimeout("snowDocument()", speed);
   } // bPaused




}//snowDocument


function pauseSnow() {
  if (bSetup == 0) { setupSnow(); }
  if (bPaused == 0) { 
    tmpspeed = speed;
    speed = 9999999999999; 
    bPaused = 1;
    }
  else { 
    speed = tmpspeed;
    bPaused = 0;
    snowDocument();
  }
}

function startstopSnow() {
    if (bSetup == 0) { setupSnow(); } 
    if (bPaused == 0) { pauseSnow(); 
    resetSnow();
    } 
    else if (bPaused == 1) { pauseSnow(); } 
}


function destructFlake(flakeid) {
  if (bExplode == 1) {
  //alert(document.getElementById('imgflake' + flakeid).src);
  document.getElementById('imgflake' + flakeid).src = imgExplode;
  }
}

setupSnow();
if (bStartImmediately == 1) { snowDocument(); }
else { bPaused = 1; } 

//-->


