/*
 * jQuery Illuminate v0.7 - http://www.tonylea.com/
 *
 * Illuminate elements in jQuery, Function takes the background color of an element
 * and illuminates the element.
 *
 * TERMS OF USE - jQuery Illuminate
 * 
 * Open source under the BSD License. 
 *
 * Currently incompatible with FireFox v.4
 * 
 * Copyright © 2011 Tony Lea
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 	
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
*/

(function($){
	$.fn.illuminate = function(options) {
	    
	    /* set the defaults */
		var defaults = {
			intensity: '0.05',
			color: '',
			blink: 'true',
			blinkSpeed: '600',
			outerGlow: 'true',
			outerGlowSize: '30px',
			outerGlowColor: ''
		};
	  
	  	/* extend the defaults and the user options */
		var options = $.extend(defaults, options);
		
		var original_color = '';
		var new_color = '';
		var dead = false;
		
		/* kill the illumination */
		$.fn.illuminateDie = function()
		{
			dead = true;
			options.intensity = '0.05';
			options.color = '';
			options.blink = 'true';
			options.blinkSpeed = '600';
			options.outerGlow = 'true';
			options.outerGlowSize = '30px';
			options.outerGlowColor = '';
			$(this).css({'boxShadow': '0px 0px 0px', 'background-color': "#" + original_color});
		}
		
		function toggleIllumination(obj, original_color, new_color, outerGlow)
		{
			if(rgb2hex(obj.css('background-color')).toUpperCase() == original_color.toUpperCase())
			{	
				
				obj.animate({"background-color": "#" + new_color, 'boxShadowBlur': outerGlow }, parseInt(options.blinkSpeed), 
					function(){
						if(!dead)
							toggleIllumination($(this), original_color, new_color, outerGlow);
					});
			}
			
			if(rgb2hex(obj.css('background-color')).toUpperCase() == new_color.toUpperCase())
			{	
				obj.animate({"background-color": "#" + original_color, 'boxShadowBlur': '0px' }, parseInt(options.blinkSpeed), 
					function(){
						if(!dead)
							toggleIllumination($(this), original_color, new_color, outerGlow);
					});
			}
		}
	
		function colorAdd(hex, percent)
		{
			percentHex = parseInt(Math.round(parseFloat(percent)*16));
			return hexAdd(hex[0], percentHex) + hexAdd(hex[1], percentHex) + hexAdd(hex[2], percentHex) + hexAdd(hex[3], percentHex) + hexAdd(hex[4], percentHex) + hexAdd(hex[5], percentHex);
			
		}
		
		function hexAdd(val, val2)
		{
			result = parseInt(val, 16) + val2;
			if(result > 15) return 'F';
			return result.toString(16).toUpperCase();
		}
	
	
	
		function rgb2hex(rgb) {
		    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		    function hex(x) {
		        return ("0" + parseInt(x).toString(16)).slice(-2);
		    }
		    return hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
		}
		
		
		
		return this.each(function() {
			obj = $(this);
			if(obj.is("input")){
				if(obj.css('border') == ''){ obj.css('border', 'none') };
			}
			dead = false;
			original_color = rgb2hex(obj.css('background-color'));
			if(options.color == ''){
				new_color = colorAdd(original_color, options.intensity);
			} else
			{
				new_color = options.color.replace('#', '');
			}
			
			var BlurColor = '';
			
			if(options.outerGlowColor == ''){
				BlurColor = new_color;
			} else {
				BlurColor = options.outerGlowColor.replace('#', '');
			}
			
			
				
			obj.css('boxShadow','0px 0px 0px #'+BlurColor);
			
			var firstColor = '';
			var firstBlur = '';
			
			if(options.blink == 'true'){
				firstColor = original_color;
				firstBlur = '0px';
			} else {
				firstColor = new_color;
				firstBlur = options.outerGlowSize;
			}
			
			var outerGlow = '';
			if(options.outerGlow == 'true'){
				outerGlow = options.outerGlowSize;
			} else {
				outerGlow = '0px';
			}
			
			obj.animate({"background-color": "#" + firstColor, 'boxShadowBlur': firstBlur }, parseInt(options.blinkSpeed), 
				function(){
					if(options.blink == 'true')
						toggleIllumination($(this), original_color, new_color, outerGlow);
				});
		});
		

	};
	
	
	
	
	/* Functionality to extend the Blur Animation */
 
    // boxShadow get hooks
    var div = document.createElement('div'),
        divStyle = div.style,
        support = $.support,
        rWhitespace = /\s/,
        rParenWhitespace = /\)\s/;

    support.boxShadow =
        divStyle.MozBoxShadow     === ''? 'MozBoxShadow'    :
        (divStyle.MsBoxShadow     === ''? 'MsBoxShadow'     :
        (divStyle.WebkitBoxShadow === ''? 'WebkitBoxShadow' :
        (divStyle.OBoxShadow      === ''? 'OBoxShadow'      :
        (divStyle.boxShadow       === ''? 'BoxShadow'       :
        false))));

    div = null;

    // helper function to inject a value into an existing string
    // is there a better way to do this? it seems like a common pattern
    function insert_into(string, value, index) {
        var parts  = string.split(rWhitespace);
        parts[index] = value;
        return parts.join(" ");
    }

    if ( support.boxShadow ) {
    
    
        $.cssHooks.boxShadow = {
            get: function( elem, computed, extra ) {
                return $.css(elem, support.boxShadow);
            },
            set: function( elem, value ) {
                elem.style[ support.boxShadow ] = value;
            }
        };

      

        $.cssHooks.boxShadowBlur = {
            get: function ( elem, computed, extra ) {
                return $.css(elem, support.boxShadow).split(rWhitespace)[5];
            },
            set: function( elem, value ) {
                elem.style[ support.boxShadow ] = insert_into($.css(elem, support.boxShadow), value, 5);
                
            }
        };

        $.fx.step[ "boxShadowBlur" ] = function( fx ) {
            $.cssHooks[ "boxShadowBlur" ].set( fx.elem, fx.now + fx.unit );
        };
    }

})(jQuery);

/**
Vertigo Tip by www.vertigo-project.com
Requires jQuery
*/
this.vtip=function(){this.xOffset=-10;this.yOffset=10;$(".vtip").unbind().hover(function(a){this.t=this.title;this.title="";this.top=(a.pageY+yOffset);this.left=(a.pageX+xOffset);$("body").append('<p id="vtip"><img id="vtipArrow" />'+this.t+"</p>");$("p#vtip #vtipArrow").attr("src","./images/vtip_arrow.png");$("p#vtip").css("top",this.top+"px").css("left",this.left+"px").fadeIn("slow")},function(){this.title=this.t;$("p#vtip").fadeOut("slow").remove()}).mousemove(function(a){this.top=(a.pageY+yOffset);this.left=(a.pageX+xOffset);$("p#vtip").css("top",this.top+"px").css("left",this.left+"px")})};jQuery(document).ready(function(a){vtip()});

/*
Particle Emitter JavaScript Library
Version 0.3
by Erik Friend

Creates a circular particle emitter of specified radius centered and offset at specified screen location.  Particles appear outside of emitter and travel outward at specified velocity while fading until disappearing in specified decay time.  Particle size is specified in pixels.  Particles reduce in size toward 1px as they decay.  A custom image(s) may be used to represent particles.  Multiple images will be cycled randomly to create a mix of particle types.

example:
var emitter = new particle_emitter({
    image: ['resources/particle.white.gif', 'resources/particle.black.gif'],
    center: ['50%', '50%'], offset: [0, 0], radius: 0,
    size: 6, velocity: 40, decay: 1000, rate: 10
}).start();
*/

particle_emitter = function (opts) {
    // DEFAULT VALUES
    var defaults = {
        center: ['50%', '50%'], // center of emitter (x / y coordinates)
        offset: [0, 0],         // offset emitter relative to center
        radius: 0,              // radius of emitter circle
        image: 'particle.gif',  // image or array of images to use as particles
        size: 1,                // particle diameter in pixels
        velocity: 10,           // particle speed in pixels per second
        decay: 500,             // evaporation rate in milliseconds
        rate: 10                // emission rate in particles per second
    };
    // PASSED PARAMETER VALUES
    var _options = $.extend({}, defaults, opts);

    // CONSTRUCTOR
    var _timer, _margin, _distance, _interval, _is_chrome = false;
    (function () {
        // Detect Google Chrome to avoid alpha transparency clipping bug when adjusting opacity
        if (navigator.userAgent.indexOf('Chrome') >= 0) _is_chrome = true;

        // Convert particle size into emitter surface margin (particles appear outside of emitter)
        _margin = _options.size / 2;
        
        // Convert emission velocity into distance traveled
        _distance = _options.velocity * (_options.decay / 1000);
        
        // Convert emission rate into callback interval
        _interval = 1000 / _options.rate;
    })();

    // PRIVATE METHODS
    var _sparkle = function () {
        // Pick a random angle and convert to radians
        var rads = (Math.random() * 360) * (Math.PI / 180);

        // Starting coordinates
        var sx = parseInt((Math.cos(rads) * (_options.radius + _margin)) + _options.offset[0] - _margin);
        var sy = parseInt((Math.sin(rads) * (_options.radius + _margin)) + _options.offset[1] - _margin);

        // Ending Coordinates
        var ex = parseInt((Math.cos(rads) * (_options.radius + _distance + _margin + 0.5)) + _options.offset[0] - 0.5);
        var ey = parseInt((Math.sin(rads) * (_options.radius + _distance + _margin + 0.5)) + _options.offset[1] - 0.5);

        // Pick from available particle images
        var image;
        if (typeof(_options.image) == 'object') image = _options.image[Math.floor(Math.random() * _options.image.length)];
        else image = _options.image;

        // Attach sparkle to page, then animate movement and evaporation
        var s = $('<img>')
        .attr('src', image)
        .css({
            zIndex:     10,
            position:   'absolute',
            width:      _options.size + 'px',
            height:     _options.size + 'px',
            left:       _options.center[0],
            top:        _options.center[1],
            marginLeft: sx + 'px',
            marginTop:  sy + 'px'
        })
        .appendTo('body')
        .animate({
            width: '1px',
            height: '1px',
            marginLeft: ex + 'px',
            marginTop: ey + 'px',
            opacity: _is_chrome ? 1 : 0
        }, _options.decay, 'linear', function () { $(this).remove(); });

        // Spawn another sparkle
        _timer = setTimeout(function () { _sparkle(); }, _interval);
    };

    // PUBLIC INTERFACE
    // This is what gets returned by "new particle_emitter();"
    // Everything above this point behaves as private thanks to closure
    return {
        start:function () {
            clearTimeout(_timer);
            _timer = setTimeout(function () { _sparkle(); }, 0);
            return(this);
        },
        stop:function () {
            clearTimeout(_timer);
            return(this);            
        },
        centerTo:function (x, y) {
            _options.center[0] = x;
            _options.center[1] = y;
        },
        offsetTo:function (x, y) {
            if ((typeof(x) == 'number') && (typeof(y) == 'number')) {
                _options.center[0] = x;
                _options.center[1] = y;
            }
        }
    }
};
