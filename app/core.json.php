{
	"_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
	"core": {
		"default_locale": "cs_CZ"
	},
	"blocks": {
		"config": {
			"block": "core/config"
		},
		"gallery_route_postprocessor": {
			"block": "gallery/route_postprocessor"
		},
		"router": {
			"block": "core/router",
			"in_con": {
				"routes": [ "config", "routes" ],
				"gallery": [ "gallery_route_postprocessor", "postprocessor" ]
			}
		},
		"skeleton": {
			"block": "core/out/page",
			"in_val": {
				"css_link": "/app/style/main.css"
			},
			"in_con": {
				"enable": [
					"router",
					"skeleton"
				]
			}
		},
		"version": {
			"block": "core/devel/version",
			"in_val": {
				"format": "short",
				"link": "/version",
				"slot": "default",
				"slot_weight": 90
			},
			"in_con": {
				"enable": [
					"skeleton",
					"done"
				]
			}
		},
		"page": {
			"block": "core/value/block_loader",
			"in_val": {
				"output_forward": "done,title"
			},
			"in_con": {
				"block": [
					"router",
					"block"
				],
				"enable": [
					"router",
					"done"
				]
			}
		},
		"page_title": {
			"block": "core/out/set_page_title",
			"force_exec": 1,
			"in_con": {
				"title": [
					":or",
					"page",
					"title",
					"router",
					"title"
				],
				"format": [
					"router",
					"title_fmt"
				]
			}
		},
		"page_hd": {
			"block": "core/out/header",
			"in_con": {
				"text": [
					"page_title",
					"title"
				],
				"enable": [
					"skeleton",
					"done"
				]
			},
			"in_val": {
				"level": 1,
				"link": "/",
				"slot": "default",
				"slot_weight": 10
			}
		},
		"page_error": {
			"block": "core/out/message",
			"force_exec": 1,
			"in_val": {
				"is-error": 1,
				"title": "Sorry!",
				"text": "Page not found.",
				"http-status-code": 404
			},
			"in_con": {
				"enable": [
					":not",
					"page",
					"done"
				]
			}
		}
	}
}
