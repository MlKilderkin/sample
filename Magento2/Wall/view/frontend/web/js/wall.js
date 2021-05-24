require(["jquery"], function($){
	(function ($) {
		"use strict";
		$('.wall-container').hover(function(){
			$(this).find('.overlay').hide();
		}, function() {
			$(this).find('.overlay').show();
		})

		$('.wall-container').on('click', function(e){
			e.preventDefault();
			$('.show-wall').find('#img-wr').empty();
			$('.show-wall').find('#content-wr').empty();

			$('#img-wr').css('background-image', 'url('+ $(this).find('img').attr('src') +')');
			$(this).find('.text').clone().appendTo('#content-wr');
			$(this).find('.hidden-content *').clone().appendTo('#content-wr');
			$('.wall-list-wrapper').css('min-height', $(window).height()-$('.page-header').height()-$('.block-footer-top').height());
			$('#show-wall').fadeIn();
		});
		$('#show-walls').on('click', function() {
			$('#show-wall').fadeOut();
			$('.wall-list-wrapper').css('min-height',0);
		})
	})(jQuery);
});

