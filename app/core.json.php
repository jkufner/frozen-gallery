{
	"_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
	"core": {
		"default_locale": "cs_CZ"
	},
	"debug": {
		"cascade_graph_slot": "html_body"
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
		"content": {
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
		"skeleton": {
			"block": "skeleton",
			"in_con": {
				"enable": [ "router", "skeleton" ]
			}
		},
		"page_type": {
			"block": "core/out/set_type",
			"force_exec": 1,
			"in_con": {
				"type": [ "router", "type" ]
			}
		}
	}
}
