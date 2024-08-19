<!--//

  /*****

  snow2.js, version 1.3, 2006-12-22
  ben@thelocust.org 
  http://thelocust.org
  
  adapted from http://gobi.gmxhome.de/snow/
  tested in IE7,IE6, Firefox 1.5, Opera 9.10

  CHANGELOG

  2006-12-22
  * update flake height detection for Opera
  * update snowflake "reset" on explode to place flakes at top of screen at a random X coord
  (thanks, toronto)

  2006-10-20
  * update flake removal code to allow flakes to go all the way to the bottom
    of the screen, based upon height of flake image. (thanks nick)

  2005-12-07
  * updated for cross-browser compatibility (new movment code)
  * now works in Firefox 1.5, IE6

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
var no = 12;                   // number of snowflakes
var speed = 30;                // the smaler, the faster snowflakes
var exptime = 500;		//for the length of the xplosion anima
var bStartImmediately = 1;
var imgSnowflake = "/projects/snow2/files/miniflake.gif";    // picture source
var imgExplode = "/projects/snow2/files/explode.gif";    // picture source
var flakeImage = new Image();
var explodeImage = new Image();
var bExplode = 1; //0 to NOT have the flakes explode on click, or 1 to have them explode!

// STUFF YOU SHANT WORRY ABOUT UNLESS YOU KNOW WHAT YOU ARE DOING, 
// AND EVEN THEN YOU MIGHT BREAK IT. 
var tmpspeed = speed;                // DO NOT CHANGE THIS
var bHasFallen = 0;

var dx, xp, yp;                // coordinate and position variables
var am, stx, sty;              // amplitude and step variables
var snowobj;
var i, doc_width = 1024, doc_height = 768;
var bPaused = 0;
var bStopped = 0;
var bSetup = 0;

//do initial snowflake setup
function setupSnow() {
  
  if (self.innerHeight) // all except Explorer
  {
	x = self.innerWidth;
	y = self.innerHeight;
  }
  else if (document.documentElement && document.documentElement.clientHeight)
	// Explorer 6 Strict Mode
  {
	x = document.documentElement.clientWidth;
	y = document.documentElement.clientHeight;
  }
  else if (document.body) // other Explorers
  {
	x = document.body.clientWidth;
	y = document.body.clientHeight;
  } 

  doc_width  = x;
  doc_height = y;

  dx = new Array();
  xp = new Array();
  yp = new Array();
  am = new Array();
  stx = new Array();
  sty = new Array();
  snowobj = new Array();
  expTimer = new Array();

  //preload images for Opera.  
  flakeImage.src = imgSnowflake;
  if (window.opera){ while (!flakeImage.complete) { } }

  for (i=0; i<no; i++) {         // iterate for every snowflake

   dx[i] = 0;                  // set coordinate variables
   xp[i] = Math.random()*(doc_width-50); // set position variables
   yp[i] = Math.random()*doc_height;
   am[i] = Math.random()*20;             // set amplitude variables
   stx[i] = 0.02 + Math.random()/10;     // set step variables
   sty[i] = 0.7 + Math.random();         // set step variables

   flakeBlob = "<div id=\"flake"+ i +"\" onclick=\"javascript:destructFlake("+ i +");\" style=\"" + "position:absolute; z-index:"+ (i+25) +"; visibility:visible; " + "\"><img id=\"imgflake" + i + "\" src=\""+ imgSnowflake + "\" border=0></div>";

   document.write(flakeBlob);
   snowobj[i] = xGetElementById("flake"+i);

  }//for

  //further, opera won't report the height correctly
  //until it is on the page.  
  flake_height = xGetElementById("imgflake0").height; 

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
  
   xTop(snowobj[i],-50);
  } // for

}


function snowDocument() {      // MSIE4, Opera5, Netscape5 main
   if (bSetup == 0) { setupSnow(); }

   if (bPaused == 0) {
      for (i=0; i<no; ++i) {      // iterate for every flake
	 yp[i] += sty[i];

         //make sure flake hasn't gone below bottom of viewport
         //'cause otherwise it'll pop up a scrollbar on pages w/o scrollbars
	 if (yp[i] > doc_height-flake_height) {
	    bHasFallen = 1;
	    xp[i] = Math.random()*(doc_width-am[i]-30);
	    yp[i] = -20;
	    stx[i] = 0.02 + Math.random()/10;
            sty[i] = 0.7 + Math.random();
	 } else { bHasFallen = 0; }

	 dx[i] += stx[i];
	
	 xMoveTo(snowobj[i],xp[i] + am[i]*Math.sin(dx[i]),yp[i]);

	 flakeSrc = document.getElementById('imgflake' + i).src;

	 if (flakeSrc.substr(flakeSrc.length - imgExplode.length,imgExplode.length) == imgExplode) {
	    isSploding = 1;
	 } else { isSploding = 0; }

	 if (isSploding == 1) { 
            if (isNaN(expTimer[i])) { expTimer[i] = 0; }

            expTimer[i] += speed;
            if (expTimer[i] > 500) { 
               //set flake to 50 px above the top of the viewport
	       yp[i] = -50;
               xp[i] = Math.random()*(doc_width-50);
               //forcibly move it there
               xMoveTo(snowobj[i],xp[i],yp[i]);
	       document.getElementById('imgflake' + i).src = imgSnowflake;
	       expTimer[i] = 0;
            } //if the explosion timer is ready to go off
	 } // if isSploding	 
      
      } //for
      
      setTimeout("snowDocument()", speed);
   } // bPaused

} //snowDocument


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


function xDef() {
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])=='undefined') return false;}
  return true;
}

function xStr(s) {
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])!='string') return false;}
  return true;
}

function xNum(n) {
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])!='number') return false;}
  return true;
}

function xGetElementById(e) {
  if(typeof(e)!='string') return e;
  if(document.getElementById) e=document.getElementById(e);
  else if(document.all) e=document.all[e];
  else e=null;
  return e;
}

function xMoveTo(e,iX,iY) {
  xLeft(e,iX);
  xTop(e,iY);
}
function xLeft(e,iX) {
  if(!(e=xGetElementById(e))) return 0;
  var css=xDef(e.style);
  if (css && xStr(e.style.left)) {
    if(xNum(iX)) e.style.left=iX+'px';
    else {
      iX=parseInt(e.style.left);
      if(isNaN(iX)) iX=0;
    }
  }
  else if(css && xDef(e.style.pixelLeft)) {
    if(xNum(iX)) e.style.pixelLeft=iX;
    else iX=e.style.pixelLeft;
  }
  return iX;
}

function xTop(e,iY) {
   if(!(e=xGetElementById(e))) return 0;
   var css=xDef(e.style);
   if(css && xStr(e.style.top)) {
      if(xNum(iY)) {
	 e.style.top=iY+'px';
      } else {
	 iY=parseInt(e.style.top);
	 if(isNaN(iY)) iY=0;
      }
   } else if(css && xDef(e.style.pixelTop)) {
   
      if(xNum(iY)) e.style.pixelTop=iY;
      else iY=e.style.pixelTop;
   }
  return iY;
}


//window.onload = setupSnow();
setupSnow();
if (bStartImmediately == 1) { snowDocument(); }
else { bPaused = 1; } 

//-->


