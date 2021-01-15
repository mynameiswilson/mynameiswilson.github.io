<!DOCTYPE html> 
<html lang="en"> 
<head class="html5reset-bare-bones"> 
	<meta charset="utf-8"> 
	<title></title> 
	
	<meta name="description" content=""> 
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script> 
	<link rel="stylesheet" href="_/css/main.css"> 
	<link rel="stylesheet" href="_/css/_print/main.css" media="print"> 
	
	<!--[if IE]>
	<link rel="stylesheet" href="_/css/_patches/win-ie-all.css">
	<![endif]--> 
	<!--[if IE 7]>
	<link rel="stylesheet" href="_/css/_patches/win-ie7.css">
	<![endif]--> 
	<!--[if lt IE 7]>
	<link rel="stylesheet" href="_/css/_patches/win-ie-old.css">
	<![endif]--> 
	<style type="text/css">
	 	#control { position: absolute; top: 0; left: 0; border: 1px solid #ccc; padding: .25em; font: 1em/1.5em arial, helvetica, sans-serif; } 
		#main { width: 600px; margin: 2em auto; }
		#box { font-size: 0; width: 550px; height: 550px; margin: -10px 0 0 -10px;}
		#box .widget { float: left; width: 55px; height: 55px; position: relative; } 
		#box .widget a { display: block; height: 75px; width: 75px; position: absolute; clip: rect(10px, 65px, 65px, 10px); overflow: hidden; border: 0; background: #000;} 

		#box .widget a img { opacity: 0.5; border: 0}
	</style> 
	<!--[if IE]>
	<style type="text/css">
	#box .widget a { z-index: -1; clip: rect(10px 65px 65px 10px); } /* IE requires no commas in clip specs */ 
	#box .widget a img { filter: alpha(opacity=50) } /* IE6/7 alpha filter opacity */
	</style>
	<![endif]-->
	<script type="text/javascript"> 
		$.getScript('jquery.animate.clip.js'); 
		$.getScript('jquery.2dtransform.js');
		
		var get_flickr_url = function(type,photo,size) {
			if (type == "img") return "http://farm"+photo.farm+".static.flickr.com/"+photo.server+"/"+photo.id+"_"+photo.secret+"_"+size+".jpg";
			if (type == "link") return "http://www.flickr.com/photos/"+photo.owner+"/"+photo.id;
		}; 
		
		var butter_rolls = function() {
			
			$('#box .widget a').hover(
			function() { //mouse over
				//set z-indexes on the widgets and not the widget a, which is clipped
				//clipped elements need to be absolutely positioned
				//and IE6/7 won't z-index absolutely positioned things correctly
				//so we set the z-index on the .WIDGET and not the .WIDGET A
				$('#box .widget').css("z-index","97"); 	//slam all the rest to the back
				$(this).parent().css("z-index","99");             	//put this one in front of all 
				var a = {'clip':'rect(0px 75px 75px 0px)'}; //base clip expand anim
				
				if ($('#rotate:checked').val() == "y") 	a.rotate = '+=360deg';	//if we are rotating, add to anim
				if ($('#flip:checked').val() == "y") { 
					a.scaleY = 1;
					a.scaleX = 0;
					a.skewY = -12.5;
					a.origin = [50,0];
				}
		   
				$(this).stop().animate(a, 500,"linear",function() {
					if ($('#flip:checked').val() == "y") {
						$(this).transform({scaleY:1,scaleX:0,skewY:12.5});
						$(this).animate({scaleY:1,scaleX:1,skewY:0},500,"linear");
					}
				}); 	//do the anim
				$("img",this).animate({'opacity':'1'},500,"linear"); //AND do the opacity on the image
			},
			function() { //mouse out
				$(this).parent().css("z-index","98"); //above the rest, but not above one mouseovering next to it 
				var a = {'clip':'rect(10px 65px 65px 10px)'};
				//if ($('#rotate:checked').val() == "y") a.rotate = '360deg';
				$(this).stop().animate(a, 500, "linear"); 
				$("img",this).animate({'opacity':'.5'},500,"linear");
				
			});
		};

		
		$(document).ready(function() {
				$.getJSON('http://api.flickr.com/services/rest/?method=flickr.interestingness.getList&api_key=7d097efce077b9e20275c9928c46e340&format=json&jsoncallback=?',function(data) {
					
				$(data.photos.photo).each(function(i,val) {
					photo = val;
					$('#box').append('<div id="widget'+i+'" class="widget"><a href="'+get_flickr_url("link",photo)+'"><img src="'+get_flickr_url("img",photo,'s')+'" /></a></div>');
				});
				
				butter_rolls();  //set up the mouseover/out functions
				
				var i = 0;
				var auto_roller;

				var start_auto = function() { setInterval( function() { $('#widget'+i+' a').mouseover(); $('#widget'+(i-1)+' a').mouseout(); i++; if(i>100) i=0; },1000); };

				$('#auto').change(function() { if ($("#"+this.id+":checked").val() == "y") {  start_auto(); } });	
				//kill the auto rollover on mouseover of the box   
				$('#box').mouseover(function() {
					clearInterval(auto_roller); 
					$('#auto').attr("checked","");
					$('#widget'+(i-1)+' a').mouseout(); //return currently mouseovered/unclipped image to normal
				}); 
				
				start_auto();			
	                                 
			}); //getJSON
		}); //document.ready
  </script> 
</head> 
 
<body> 
 
<div id="control">
	<input type="checkbox" id="rotate" value="y" /> rotate?<br/>
	<input type="checkbox" id="flip" value="y" /> flip?
	<input type="checkbox" id="auto" checked="checked" value="y" /> auto?
</div>
<div id="main"> 
	<div id="box"></div> 
</div> 
 
</body> 
</html>
