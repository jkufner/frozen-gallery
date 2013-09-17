{
	"php": {
		"log_errors": 1,
		"html_errors": "",
		"display_errors": "",
		"error_reporting": 32767,
		"ignore_repeated_errors": 1
	},
	"core": {
		"default_locale": "cs_CZ",
		"app_init_file": "app/init.php"
	},
	"debug": {
		"debug_logging_enabled": 1,
		"always_log_banner": 1,
		"log_memory_usage": 1,
		"add_cascade_graph": 1,
		"cascade_graph_link": "/doc/%s",
		"profiler_stats_file": "var/profiler.stats"
	},
	"output": {
		"default_type": "html5"
	},
	"define": [

	],
	"module-map": [

	],
	"block:router": {
		".block": "core/ini/router",
		"config": "app/routes.ini.php"
	},
	"block:skeleton": {
		".block": "core/out/page",
		"css_link": "/app/style/main.css",
		"enable": [
			"router:skeleton"
		]
	},
	"block:version": {
		".block": "core/devel/version",
		"format": "short",
		"link": "/version",
		"slot": "default",
		"slot_weight": 90,
		"enable": [
			"skeleton:done"
		]
	},
	"block:page": {
		".block": "core/value/cascade_loader",
		"output_forward": "done,title",
		"content": [
			"router:content"
		],
		"enable": [
			"router:done"
		]
	},
	"block:page_title": {
		".block": "core/out/set_page_title",
		".force-exec": 1,
		"title": [
			"page:content_0_title"
		],
		"title_fallback": [
			"router:title"
		],
		"format": [
			"router:title_fmt"
		]
	},
	"block:page_hd": {
		".block": "core/out/header",
		"text": [
			"page_title:title"
		],
		"level": 1,
		"link": "/",
		"enable": [
			"skeleton:done"
		],
		"slot": "default",
		"slot_weight": 10
	},
	"block:page_error": {
		".block": "core/out/message",
		".force-exec": 1,
		"is-error": 1,
		"title": "Sorry!",
		"text": "Page not found.",
		"http-status-code": 404,
		"hide": [
			"page:content_0_done"
		]
	}
}
