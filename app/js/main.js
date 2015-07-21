$(document).ready(function() 
{
	$('.gallery_listing a.item').fancybox({
		padding: 0,
		mouseWheel: false,
		loop: false,
		closeBtn: false,
		arrows: false,
		nextClick: true,
		openEffect: 'none',
		closeEffect: 'none',
		nextEffect: 'none',
		prevEffect: 'none',
		helpers : {
			overlay : {
				css : {
					'background' : 'rgba(0, 0, 0, 0.80)'
				}
			}
		}
	});
});

