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

	map.on('fullscreenchange', function () {
		if (map.isFullscreen()) {
			map.zoomIn(1, { animate: false });
		} else {
			map.zoomOut(1, { animate: false });
		}
	});

	var track_layer = L.layerGroup().addTo(map);

	var thumbnail_pane = map.createPane('thumbnail');
	var thumbnail_layer = L.layerGroup().addTo(map);
	thumbnail_pane.style.zIndex = 650;

	//console.info('Loading exiftool.json:', exiftool_json_url);
	//console.log('img_base = ', img_base);

	var bb = L.latLngBounds();
	function fitMapView() {
		if (bb.isValid()) {
			map.fitBounds(bb, { animate: false, maxZoom: 13 });
			console.log('Map view: %s, zoom %d.', map.getCenter().toString(), map.getZoom());
		}
	}

	// Add images to the map
	$('.gallery_listing a.thumbnail').each(function(i, el) {
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
					pane: 'thumbnail',
					icon: L.divIcon({
						iconSize: [ 44, 44 ],
						html: '<div style="background-image: url(' + encodeURI(tb_src) + ');"></div>',
						className: 'map_thumbnail_marker',
					})
				})
				.on('click', function(ev) {
					console.log('Click:', $el, ev);
					$el.click();
				})
				.addTo(thumbnail_layer);
			bb.extend(ll);
		}
	});

	fitMapView();

	// Add GPX files to the map
	$('.gallery_listing a.file_link').each(function(i, el) {
		var $el = $(el);
		var gpx_url = $el.attr('href');
		if (gpx_url.match(/\.gpx$/)) {
			var gpx = new L.GPX(gpx_url, {
					marker_options: {
						startIcon: L.colorIcon({ color: '#af4' }),
						endIcon: L.colorIcon({ color: '#fa4' }),
						wptIcons: {
							'': L.colorIcon({ color: '#6af' }),
							'Geocache': L.colorIcon({ color: '#6b0' }),
							'Parking Area': L.colorIcon({ color: '#ccc' }),
							'Reference Point': L.colorIcon({ color: '#ddd' }),
							'Trailhead': L.colorIcon({ color: '#bdb' }),
						}
					},
					Icon: L.colorIcon({ color: '#dfd' }),
					polyline_options: { color: '#d0a' },
					async: true,
				})
				.on('loaded', function(ev) {
					bb.extend(ev.target.getBounds());
					fitMapView();
				})
				.addTo(track_layer);
		}
	});
}


$(document).ready(function() 
{
	// Initialize gallery
	$('.gallery_listing a.thumbnail').magnificPopup({
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

	// Initialize map
	if ($('#gallery_map').length) {
		initializeMap('gallery_map');
	}

	// Progress bar
	(function() {
		var gallery_selector = '.gallery_listing a.thumbnail img';
		var gallery_image_count = $(gallery_selector).length;
		var progress_bar = null;
		var refresh_interval = 0.35; // seconds

		if (gallery_image_count == 0) {
			// Nothing to load
			return;
		}

		function set_progressbar(part, total) {
			if (!progress_bar) {
				progress_bar = $('<div></div>').css({
					'position': 'absolute',
					'top': '100%',
					'left': 0,
					'width': 0,
					'height': 0,
					'background': '#888',
					'color': '#888',
					'white-space': 'nowrap',
					'text-align': 'left',
					'padding-top': '0.5em',
					'opacity': 1,
					'transition': 'width ' + refresh_interval + 's linear,'
						+ ' opacity ' + 1.5 * refresh_interval + 's ease-out',
				});
				$('header').append(progress_bar);
			}
			if (gallery_image_count > 0) {
				progress_bar.css('width', total > 0 ? 100. * (part / total) + '%' : 0);
				progress_bar.text(" Loading: " + part + ' / ' + total);
			}
		}

		function remove_progressbar() {
			progress_bar.css('opacity', 0);
			setTimeout(function() {
				clearInterval(loading_interval);
				if (progress_bar) {
					progress_bar.remove();
					progress_bar = null;
				}
			}, 1.5 * refresh_interval * 1000);
		}
		
		set_progressbar(0, 0);

		var loading_interval = setInterval(function() {
			var n = 0;
			$(gallery_selector).each(function() {
				if (this.complete) {
					n++;
				}
			});
			if (n == gallery_image_count) {
				remove_progressbar();
			} else {
				set_progressbar(n, gallery_image_count);
			}
		}, refresh_interval * 1000);

		$(window).one('load', function() {
			set_progressbar(gallery_image_count, gallery_image_count);
			remove_progressbar();
		});
	})();

});

