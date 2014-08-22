$(document).ready(function() {

    $('head').append(
        $("<style>")
            .attr('id','styleTween')
    );
    
    $('#buttons button').click( function() {    
        switch_styles( $($(this).data("style")).html(), ($(this).data("wrapper"))?$(this).data("wrapper"):'' );
    });
});

//pull selectors from a JSCSSP parser result
function get_selectors(foo) {
    if (typeof foo == "object" && foo != null && foo.hasOwnProperty("cssRules") ) {
        var selectors = [];
        for (var h=0;h<foo.cssRules.length;h++) {
            var r = foo.cssRules[h];
            if (r.hasOwnProperty("mSelectorText")) {
                selectors.push(r.mSelectorText);
            }
        }
        selectors.sort();
        return selectors;
    } else return [];
 
}

function switch_styles(new_style_css,wrapper) {

    var parser = new CSSParser();
    var current_style = parser.parse($('#styleTween').html());
    var new_style = parser.parse(new_style_css);
    var old_style_selectors = [];
    var new_style_selectors = [];
    var common_selectors = [];
    var only_in_old_selectors = [];
    var only_in_new_selectors = [];
    var to_be_animated = [];
    var selectors_affected = [];
    

    if (new_style) {
//        if (current_style != null && current_style.hasOwnProperty("cssRules") && current_style.cssRules[0].hasOwnProperty("error") ) { return; }    
        if (new_style != null && new_style.hasOwnProperty("cssRules") && new_style.cssRules[0].hasOwnProperty("error") ) { return; }

        old_style_selectors = get_selectors(current_style);
        new_style_selectors = get_selectors(new_style);

        //find declarations (#foo, #bar, .wamp) that we need to deal withi
        if (old_style_selectors) {
            only_in_old_selectors = old_style_selectors.diff(new_style_selectors);
        }

        if (new_style_selectors) {
            only_in_new_selectors = new_style_selectors.diff(old_style_selectors);
        }
        common_selectors = old_style_selectors.intersection(new_style_selectors);

        $.each(only_in_old_selectors,function(i,val) {
            if ( val.indexOf(",") >=0 ) {
                var split_selector = val.split(",");
                only_in_old_selectors.concat(split_selector);
                only_in_old_selectors.remove(i);
            }    
        });
        selectors_affected = only_in_old_selectors;


        //find style rules in the NEW style that we need to change, from this list of common declarations            
        var declarations_to_be_animated = new_style.cssRules.filter(function(val) { 
            return 0 != in_array(val.mSelectorText, common_selectors);
        });    

        var animation_queue = [];

        $.each(declarations_to_be_animated,function(i,s) {
            selectors_affected.push(s.mSelectorText);
            
            var anim_props = {};
            var nonanim_props = {};
           
            if ( s.hasOwnProperty("declarations")) {  
                $.each(s.declarations,function(j,dec) {
                    
                    var val = parseFloat(dec.valueText);
                    if (!isNaN(val)) {
                        anim_props[ camel_case(dec.property) ] = dec.valueText;   
                    } else {
                        //console.log(s.declarations[i].property + " is not numeric");          
                        nonanim_props[ camel_case(dec.property) ] = dec.valueText;                           
                    }
                    
                });
            }
            animation_queue.push({
                selector: s.mSelectorText,
                nonanim_props: nonanim_props,
                anim_props: anim_props
                });


        });

        do_animation_queue( animation_queue, function() {
            //set new style in stone              
            if (wrapper.indexOf('{0}') >= 0) {
                //    new_style_css = wrapper.format(new_style_css);
            }                                                    
            $('#styleTween').html(new_style_css);        
        
            //remove any inline declarations                                                    
            $(selectors_affected.join()).removeAttr('style');
                                                    
        });

    }
    
}        
  
function do_animation_queue(animation_queue,callback) {
    console.log("do animations queue called:");
    console.log(animation_queue);
    if (animation_queue.length == 0) { callback(); return; }
    var animations_complete = 0;
    $.each(animation_queue,function(i,a) {
        //set non-animatable props
        $(a.selector).css(a.nonanim_props);
        //animate animateable properties
        $(a.selector).animate(a.anim_props,{})
            .promise().done( 
                    function() {  
                        animations_complete++;
                        if (animations_complete == animation_queue.length) { callback(); } 
                        } );  
    });

}


function camel_case(str) {
    return str.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase() });    
}
    
function in_array(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

String.prototype.format = function() {
  var args = arguments;
  return this.replace(/{(\d+)}/g, function(match, number) { 
    return typeof args[number] != 'undefined'
      ? args[number]
      : match
    ;
  });
};
                                       

Array.prototype.remove = function(from, to) {
      var rest = this.slice((to || from) + 1 || this.length);
        this.length = from < 0 ? this.length + from : from;
          return this.push.apply(this, rest);
};

Array.prototype.diff = function(a) {
    return this.filter(function(i) {return !(a.indexOf(i) > -1);});
};

Array.prototype.intersection = function(b) {

  var result = new Array();
  while( this.length > 0 && b.length > 0 )
  {  
     if      (this[0] < b[0] ){ this.shift(); }
     else if (this[0] > b[0] ){ b.shift(); }
     else /* they're equal */
     {
       result.push(this.shift());
       b.shift();
     }
  }

  return result;
}
