$.fn.amigoSorter = function(options) {

	var settings = $.extend({
		li_helper: "li_helper",
		li_empty: "empty",
		onTouchStart : function() {},
		onTouchMove : function() {},
		onTouchEnd : function() {}
	}, options );

	var action = false;
	var li_index = null;
	var $ul = null;
	var shift_left = 0;
	var shift_top = 0;
	var mouse_up_events = ( $.mobile ) ? 'mouseup vmouseup' : 'mouseup';
	var mouse_move_events = ( $.mobile ) ? 'mousemove vmousemove' : 'mousemove';
	var mouse_down_events = ( $.mobile ) ? 'mousedown vmousedown' : 'mousedown';

	$(document.body).append( $.fn.amigoSorter.li_helper( settings.li_helper ) ); 

	$(document).on(mouse_up_events, function(e) {
		e.preventDefault(); 
		settings.onTouchEnd.call();
		action = false;
		$ul.find('li').removeClass(settings.li_empty);
		$('.' + settings.li_helper).css('display','none').html('');
	});

	return this.each(function() {
		$ul = $(this);
		$(document).on(mouse_move_events, function(e) {
			settings.onTouchMove.call();
			if (action == true) {
				$('.' + settings.li_helper).css('left', e.pageX).css('top', e.pageY);
				$.fn.amigoSorter.set_li_helper_pos( settings.li_helper, e, shift_left, shift_top);	

				$ul.children('li').each( function() {
					var $li = $(this);
					if (!$li.hasClass(settings.li_empty)) {
						var $li_offset = $li.offset();
						var start_left = $li_offset.left;
						var start_top = $li_offset.top;
						var end_left = $li_offset.left + $li.outerWidth();
						var end_top = $li_offset.top + $li.outerHeight();

						if ( e.pageX > start_left && e.pageX < end_left && e.pageY > start_top && e.pageY < end_top ) {
							var hover_index = $li.index();
							var shift_count = Math.abs(hover_index - li_index);
							for (i = 1; i<=shift_count; i++) {
								if (hover_index >= li_index) { 
									$ul.children('li').eq(li_index).insertAfter($ul.children('li').eq(li_index + 1));
									li_index++;
								}
								else { 
									$ul.children('li').eq(li_index - 1).insertAfter($ul.children('li').eq(li_index)); 
									li_index--;
								}
							}
						}
					}
				});
			}
		});

		$($ul.find('li')).on(mouse_down_events, function(e) {
			e.preventDefault(); 
			e.stopImmediatePropagation();
			settings.onTouchStart.call();
			var $li = $(this);
			$ul = $li.closest('ul');
			var li_offset = $li.offset();
			shift_left = e.pageX - li_offset.left;
			shift_top = e.pageY - li_offset.top;
			var li_html = $li.html();
			li_index = $li.index();
			$li.addClass(settings.li_empty);
			$.fn.amigoSorter.set_li_helper_size( $ul, $li, settings.li_helper);	
			$.fn.amigoSorter.set_li_helper_pos( settings.li_helper, e, shift_left, shift_top);	
			$('.' + settings.li_helper).html(li_html).css('display','inline-block');
			action = true;
		});
	});

};

$.fn.amigoSorter.li_helper = function( helper_class ) {
	return '<span class="' + helper_class + '"></span>';
};

$.fn.amigoSorter.set_li_helper_size = function( $ul , $li, helper_class ) {
	var width = $li.outerWidth();
	var height = $li.outerHeight();
	$('.' + helper_class).css('width', width + 'px').css('height', height + 'px');
	return true;
};

$.fn.amigoSorter.set_li_helper_pos = function( helper_class, e, shift_left, shift_top ) {
	$('.' + helper_class).css('left', e.pageX - shift_left ).css('top', e.pageY - shift_top );	
};
