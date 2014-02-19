{
	"_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
	"defaults": {
		"title_fmt": "%s - gallery.frozen-doe.net",
		"title": "(missing title)",
		"type": "html5",
		"block": null,
		"block_fmt": null,
		"connections": {
		}
	},
	"groups": {
		"admin": {
			"require": {
				"block_allowed": "admin/main"
			},
			"defaults": {
				"skeleton": false
			},
			"routes": {
				"/admin": {
					"title": "Administration",
					"block": "admin/main"
				},
				"/admin/**": {
					"title": "Administration",
					"block": "admin/main",
					"connections": {
						"path": [
							"router",
							"path_tail"
						]
					}
				}
			}
		},
		"pages": {
			"require": {
			},
			"defaults": {
				"skeleton": true
			},
			"routes": {
				"/": {
					"title": "gallery.frozen-doe.net",
					"block": "page/index",
					"connections": {
					}
				},
				"/version": {
					"title": "Profiler",
					"block": "page/version"
				},
				"/profiler": {
					"title": "Profiler",
					"block": "page/profiler"
				}
			}
		},
		"gallery": {
			"defaults": {
				"skeleton": true
			},
			"routes": {
				"/$gallery": {
					"title": "Gallery",
					"block": "page/gallery",
					"connections": {
					}
				}
			}
		},
		"gallery_content": {
			"defaults": {
				"skeleton": true
			},
			"postprocessor": "gallery",
			"routes": {
				"/$gallery/**": {
					"title": "Gallery"
				}
			},
			"extensions": {
				".tb.jpg": {
					"skeleton": false,
					"block": "page/thumbnail",
					"size": 120
				}
			},
			"file_checks": {
				"is_dir": {
					"block": "page/gallery"
				},
				"is_file": {
					"skeleton": false,
					"block": "page/thumbnail",
					"size": 800,
					"is_file": true
				}
			}
		},
		"thumbnails": {
			"require": {
			},
			"defaults": {
				"skeleton": false
			},
			"routes": {
				"/preview/**": {
					"title": "Preview",
					"block": "page/thumbnail",
					"connections": {
					},
					"size": 600
				}
			}
		}
	},
	"reverse_routes": {
		"admin": {
			"url": "/admin/{block}",
			"args": [
				"block"
			]
		},
		"documentation": {
			"url": "/documentation/block/{block}",
			"args": [
				"block"
			]
		}
	}
}
