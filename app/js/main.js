$(document).ready(function() 
{
	$('.gallery_listing a.item').magnificPopup({
		type:'image',
		gallery: {
			enabled: true
		},
		image: {
			titleSrc: function(item) {
				return item.el.find('span.name').text();
			}
		}
	});
});

