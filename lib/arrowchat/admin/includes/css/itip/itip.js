(function ($)
{
	//Embed the animate.css and modinzr plugins
	var methods = 
	{
		//Initialize function
		init : function(options)
		{	
			//Default settings
			var settings = $.extend( 
	  		{
				'closeButton' : 'true',
				'direction' : 'right',
				'marginVer' : '30',
				'marginHor' : '5',
				'icons' : [
					{
						'id' : 'thumbsup'
					}
				]
	    	}, options);

			//Element 
			var $el = $(this);
		    var $elementWidth = $el.outerWidth();
		    var $elementHeight = $el.outerHeight();
		    var $elementOffsetLeft = $el.offset().left;
		    var $elementOffsetTop = $el.offset().top;
		    var $marginVer = settings.marginVer;
		    var $marginHor = settings.marginHor;

			//[END] SETTINGS
			
			var $iconsHTML = '';
			//Loop through the icon settings
			$(settings.icons).each(function(index, value)
			{
				//Generate HTML for the icons
				var $divID = value.id; 
				$iconsHTML += '<li class="icon" title="Download" id='+$divID+'></li>';
			});
			
			//If closebutton is set to true, add it
			if (settings.closeButton == 'true')
			{
				if (settings.direction == "left")
					{
						oldIconsHTML = $iconsHTML;
						$iconsHTML = '<li title="Download" class="icon close" id="close-left"></li>'+oldIconsHTML;
					}
				if (settings.direction == "right" || settings.direction == "top" || settings.direction == "bottom")
					{
						$iconsHTML += '<li title="Download" class="icon close" id="close-right"></li>';
					}
			}
			
			//Triangle settings
			var $triangleHTML = '';
			switch (settings.direction)
			{
				case 'right':
					$triangleHTML = '<div class="triangle-border triangle-right-border"></div><div class="triangle triangle-right"></div>';
					break;
				case 'left':
					$triangleHTML = '<div class="triangle-border triangle-left-border"></div><div class="triangle triangle-left"></div>';
					break;
				case 'bottom':
					$triangleHTML = '<div class="triangle-border triangle-bottom-border"></div><div class="triangle triangle-bottom"></div>';
					break;	
				case 'top':
					$triangleHTML = '<div class="triangle-border triangle-top-border"></div><div class="triangle triangle-top"></div>';
					break;	
			}
			
			//Create the tooltip
			$el.after('<div class="itip-tooltip animated" id="test">'+$triangleHTML+'<ul class="icons">'+$iconsHTML+'</ul></div>');
			var $tooltip = $el.next('.itip-tooltip');
			var $tooltipWidth = $tooltip.outerWidth();
			var $tooltipHeight = $tooltip.outerHeight();
			var $triangleWidth = $tooltip.find('.triangle').outerWidth();
														
			switch (settings.direction)
			{
				case 'right':
					$tooltip.css('margin-top', $elementOffsetTop+($elementHeight/2)-($tooltipHeight/2));
					$tooltip.css('margin-left', $elementOffsetLeft+$elementWidth+30);
					break;
				case 'left':
					$tooltip.css('margin-top', $elementOffsetTop+($elementHeight/2)-($tooltipHeight/2));
					$tooltip.css('margin-left', $elementOffsetLeft-$tooltipWidth-$marginVer);
					$tooltip.find('.triangle-left').css('left', $tooltipWidth-3);
					$tooltip.find('.triangle-left-border').css('left', $tooltipWidth-2);
					break;
				case 'bottom':
					$tooltip.css('margin-top', ($elementOffsetTop+$elementHeight)-$marginHor+($tooltipHeight/2));
					$tooltip.css('margin-left', ($elementOffsetLeft+($elementWidth/2))-($tooltipWidth/2));
					$tooltip.find('.triangle-bottom').css('left', ($tooltipWidth/2)-($triangleWidth/2));
					$tooltip.find('.triangle-bottom-border').css('left', ($tooltipWidth/2)-($triangleWidth/2));
					break;
				case 'top':
					$tooltip.css('margin-top', $elementOffsetTop-$marginHor);
					$tooltip.css('margin-left', ($elementOffsetLeft+($elementWidth/2))-($tooltipWidth/2));
					$tooltip.css('top', '-50px');
					$tooltip.find('.triangle-top').css('left', ($tooltipWidth/2)-($triangleWidth/2));
					$tooltip.find('.triangle-top-border').css('left', ($tooltipWidth/2)-($triangleWidth/2));
					break;	
			}

			//Correctly align icons
			$tooltip.find('.icons').css('margin-top', '-30px');

			//Apply animations
			var $showAnimation = '';
			var $closeAnimation = '';
			
			if (typeof settings.show == 'undefined') //If setting is not set, set animation that fits the direction
			{
				switch (settings.direction)
				{
					case 'right':
						$showAnimation = 'fadeInLeft';
						break;
					case 'left':
						$showAnimation = 'fadeInRight';
						break;	
					case 'bottom':
						$showAnimation = 'fadeInUp';
						break;
					case 'top':
						$showAnimation = 'fadeInDown';
						break;								
				}
			}
			else
			{
				$showAnimation = settings.show;
			}

			if (typeof settings.close == 'undefined')
			{
				switch (settings.direction)
				{
					case 'right':
						$closeAnimation = 'fadeOutLeft';
						break;
					case 'left':
						$closeAnimation = 'fadeOutRight';
						break;	
					case 'bottom':
						$closeAnimation = 'fadeOutUp';
						break;
					case 'top':
						$closeAnimation = 'fadeOutDown';
						break;								
				}
			}
			else
			{
				$closeAnimation = settings.close;
			}
			
			if (Modernizr.cssanimations) 
			{
				$tooltip.addClass($showAnimation);
			}
			else
			{
				$tooltip.hide().fadeIn();
			}
			
			//Handle close button action
			if (settings.closeButton == 'true')
			{	
				$tooltip.find('.close').on('click', function()
				{
					if (Modernizr.cssanimations) 
					{
						$tooltip.addClass($closeAnimation);
					}
					else
					{
						$tooltip.fadeOut();
					}	
					
					if (jQuery.browser.webkit)
					{
						$tooltip.on("webkitAnimationEnd", function(event)
						{
							$tooltip.remove();
						});	
					}
					
					//Mozilla
					if (jQuery.browser.mozilla)
					{
						$tooltip.on('animationend', function(event)
						{
							$tooltip.remove();
						});
					}
					
					//Opera
					if (jQuery.browser.opera)
					{
						$tooltip.on("oAnimationEnd", function(event)
						{
							$tooltip.remove();
						});	
					}
					
				});
			}
			
			//If a setting has a click function, enable the callback
			$(settings.icons).each(function(index, value)
			{
				if (typeof value.click == 'function')
				{
					$tooltip.find('#'+value.id).on('click',function()
					{
						value.click.call(this);
					});
				}
				
				if (typeof value.hover == 'function')
				{
					$tooltip.find('#'+value.id).on('mouseenter',function()
					{
						value.hover.call(this);
					});
				}				
			});
		},
	};
	
	//Method calling
	$.fn.iTip = function( method ) {

	    if ( methods[method] ) {
	      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
	    } else if ( typeof method === 'object' || ! method ) {
	      return methods.init.apply( this, arguments );
	    } else {
	      $.error( 'Method ' +  method + ' does not exist on jQuery.iTip' );
	    }    

	  };
	
})( jQuery );