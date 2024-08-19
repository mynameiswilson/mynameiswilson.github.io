
windowOnLoad(linkPrep);
windowOnLoad(imagePrep);
windowOnLoad(dragPrep);

function popup(url,w,h) {
    var popUp = window.open(url,'popup','width='+w+',height='+h);
}

function linkPrep() {
    // Assign link events
    for (var i = 0; i < document.links.length; i++) {
	if (document.links[i].className.match('external')) {
		document.links[i].onclick = function() { offsitePopup(this.href); return false; }
        }
        if (document.links[i].className.match('thumbnail')) {
	    document.links[i].onclick = function() { thumbnailImage(this.href, this.title); return false; }
        }
    }
}

function imagePrep() {
    for (var i = 0; i < document.images.length; i++) {
	var img = document.images[i];

	if (img.className.match('thumbnail')) {
	    img.parentNode.onclick=function() { thumbnailPopup(img.parentNode.href); return false; }
	}
    }
}

function dragPrep() {
    var dragIndex = new Array();
    $$('.draggy').each(function(thing) { new Draggable(thing,{starteffect: null, endeffect: null});  });
}

function offsitePopup(url) {
    var popUp = window.open(url, 'offsiteWindow', 'width=750,height=500,scrollbars=yes,resizable=yes,menubar=yes,toolbar=yes,location=yes');
    if (typeof popUp == 'object') {
        popUp.focus();
    }
}

function thumbnailPopup(url) {
    var popUp = window.open(url, 'offsiteWindow', 'width=500,height=500,scrollbars=yes,resizable=yes,menubar=yes,toolbar=yes,location=yes');
    if (typeof popUp == 'object') {
        popUp.focus();
    }
}


