"use strict";

// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/endsWith
if (!String.prototype.endsWith) {
	String.prototype.endsWith = function(searchString, position) {
		var subjectString = this.toString();
		if (typeof position !== 'number' || !isFinite(position) || Math.floor(position) !== position || position > subjectString.length) {
			position = subjectString.length;
		}
		position -= searchString.length;
		var lastIndex = subjectString.lastIndexOf(searchString, position);
		return lastIndex !== -1 && lastIndex === position;
	};
}

function initializeMap(elementId)
{
	var map = L.map('gallery_map', {
			minZoom: 2,
			maxZoom: 18
		});

	var exiftool_json_url = $(map.getContainer()).data('exiftool_src');

	var tilelayer_local = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
			attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase,'
			+ ' Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community'
		}).addTo(map);

	map.addControl(new L.Control.Fullscreen({
		position: 'topright',
		pseudoFullscreen: true	// Gallery won't open over real fullscreen
	}));

	//console.info('Loading exiftool.json:', exiftool_json_url);
	//console.log('img_base = ', img_base);

	var bb = L.latLngBounds();
			
	$('.gallery_listing a.item').each(function(i, el) {
		var $el = $(el);
		var lat = $el.data('lat');
		var lng = $el.data('lng');
		var name = $el.find('.name').text();
		var tb_src = $el.find('img').attr('src');

		//console.log(i, name, lat, lng);
		if (lat && lng) {
			var ll = L.latLng(lat, lng);
			//L.marker(ll, { title: name }).addTo(map);
			L.marker(ll, {
					title: name,
					clickable: true,
					zIndexOffset: - i * 1000,
					icon: L.divIcon({
						iconSize: [ 44, 44 ],
						html: '<div style="background-image: url(' + encodeURI(tb_src) + ');"></div>',
						className: 'map_thumbnail_marker',
					})
				})
				.on('click', function() {
					$el.click();
				})
				.addTo(map);
			bb.extend(ll);
		}
	});

	if (bb.isValid()) {
		map.fitBounds(bb, { animate: false, maxZoom: 13 });
		console.log('Map view: %s, zoom %d.', map.getCenter().toString(), map.getZoom());
	} else {
		map.destroy();
	}
}


$(document).ready(function() 
{
	$('.gallery_listing a.item').magnificPopup({
		type:'image',
		gallery: {
			enabled: true,
			preloadAfterLoad: true
		},
		image: {
			titleSrc: function(item) {
				return item.el.find('span.name').text();
			}
		}
	});

	if ($('#gallery_map').length) {
		initializeMap('gallery_map');
	}

});

