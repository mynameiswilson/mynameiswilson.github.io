<!DOCTYPE html> 
<html lang="en"> 
<head class="html5reset-bare-bones"> 
	<meta charset="utf-8"> 
	<title>banner fanner - ben wilson dot org / shed</title> 
	<meta name="description" content=""> 
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script> 
	<link rel="stylesheet" href="_/css/main.css"> 
	<link rel="stylesheet" href="_/css/_print/main.css" media="print"> 
	
	<style type="text/css">
		#main { width: 600px; margin: 2em auto; }
	
		#banners { height: 345px; width: 500px; position: relative }
		#banners .banner { display: block; width: 871px; height: 345px; position: absolute; top: 0; left: 99999px; } 
		#banners #banner0 {  left: 0; } 
		#banners #banner_nav { position: absolute; border: 1px solid #f00; background: #fff; z-index: 99; bottom: 0; right: 0; } 
		#banners #banner_nav a { color: #ccc; font-weight: bold; padding: 0 1em; 
			font: 1.6em/1em helvetica,arial,sans-serif; 
			text-decoration: none 
			} 
		#banners #banner_nav a.selected { background: #fff; color: #c60c30 } 
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
	
		var current_banner = 0;
		var total_banners = 0;
		var animation_duration = 500;
		var slide_duration = 4000;
					
		var show_banner = function(x) {
			console.log("showing banner: " + x);

			if (x >= total_banners) { x=1; }
			// set all banners NOT current or next to z-index 80 (BOTTOM of STACK)
			$('#banners .banner').not('.current,.next').css({"z-index":"80"}); 
			//set banner nav item to selected
			$('#banner_nav a').each(function(i) { $(this).toggleClass('selected',i==x); }); 
			$('#banners .current').css("z-index","81");		// set CURRENT banner to z-index of 81, just above other banners @ 80

			var slide_banner_in = function() {
			$('#banner'+x)											//for our NEXT/SLIDING IN banner, 
				.addClass('next')									// add class of next
				.css({"left":$('#banners').width(),"z-index":"90"})				// set off to the right, set z-index to TOP
				.animate(
						{"left":"0px"},									// slide to the left over 1s
						{
							duration: animation_duration,
							complete:function() { 	//when complete, set the CURRENT to zindex of 80, remove class of current
								$(".current").css("z-index","80").removeClass("current"); 
								$(this).addClass("current").removeClass('next'); //and change NEXT to CURRENT, remove NEXT
								current_banner = x;
								}, // animation.complete
							queue: true	
						} // animate options
					);// animate

			}; //slide_banner_in

			$('#banners #banner'+(x-1)).animate(
				{rotate:-45,origin:[0,100]},
				{
					duration:250,
					complete:function(){
						slide_banner_in();
						$(this).animate({rotate:"0",origin:[0,100]},{duration:500,queue:true});
					},
					queue:true
				} // options
			);


//			$('#banners').animate({"height":$('#banner'+x+' img').height(),"width":$('#banner'+x+' img').width()},{duration:200});
		}; // show_banner
					
		var get_flickr_url = function(type,photo,size) {
			if (type == "img") {
				var link = "http://farm"+photo.farm+".static.flickr.com/"+photo.server+"/"+photo.id+"_"+photo.secret;
				if (size != undefined) link += "_"+size;
				return link += ".jpg";
			} else if (type == "link") return "http://www.flickr.com/photos/"+photo.owner+"/"+photo.id;
		}; 
		
		$(document).ready(function() {
			$.getJSON('http://api.flickr.com/services/rest/?method=flickr.interestingness.getList&per_page=5&api_key=7d097efce077b9e20275c9928c46e340&format=json&jsoncallback=?',function(data) {
					
				$(data.photos.photo).each(function(i,val) {
					photo = val;
					$('#banners').append('<a class="banner" id="banner'+i+'" href="'+get_flickr_url("link",photo)+'"><img src="'+get_flickr_url("img",photo)+'" /></a>');
					total_banners++;

					$('#banner_nav').append(	//add nav items to banner nav
						$('<a></a>')					//create link 			<a></a>
							.html(i+1)					//set link html to #x - <a>1</a>
							.attr('href','#')			//set link href to #  - <a href="#">1</a>
							.click(function() { 		//on link CLICK 
									show_banner(i); //show banner #i
									clearInterval(slideshow);
								})
							.toggleClass('selected',!i) //if this is banner #0, set selected
						); // append
				}); //photos.photo .each
			
				//start slideshow
				slideshow = setInterval(function(){show_banner(current_banner+1)},slide_duration);
				console.log("total banners: "+ total_banners);


			}); //getJSON


		}); //document.ready
  </script> 
</head> 
 
<body> 
 
<div id="main"> 

<div id="banners">	
<div id="banner_nav"></div><!--/banner_nav--> 
</div>


</div> 
 
</body> 
</html>
