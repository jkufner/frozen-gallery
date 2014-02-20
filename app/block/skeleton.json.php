{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "outputs": {
        "done": true
    },
    "blocks": {
        "version": {
            "block": "core/devel/version",
            "x": 440,
            "y": 225,
            "in_con": {
                "enable": [
                    "above",
                    "done"
                ],
                "slot": [
                    "above",
                    "name"
                ]
            },
            "in_val": {
                "format": "short",
                "link": "/version",
                "slot_weight": 90
            }
        },
        "page_title": {
            "block": "core/out/set_page_title",
            "x": 10,
            "y": 35,
            "in_con": {
                "title": [
                    ":or",
                    "content",
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
            "x": 634,
            "y": 0,
            "in_con": {
                "enable": [
                    "main",
                    "done"
                ],
                "text": [
                    "page_title",
                    "title"
                ],
                "slot": [
                    "main",
                    "name"
                ]
            },
            "in_val": {
                "level": 1,
                "link": "/",
                "slot_weight": 10
            }
        },
        "page_error": {
            "block": "core/out/message",
            "x": 0,
            "y": 412,
            "in_con": {
                "enable": [
                    ":not",
                    ".content",
                    "done"
                ]
            },
            "in_val": {
                "title": "Sorry!",
                "text": "Page not found.",
                "is-error": 1,
                "http-status-code": 404
            }
        },
        "below": {
            "block": "core/out/slot",
            "x": 219,
            "y": 339,
            "in_con": {
                "enable": [
                    "page",
                    "done"
                ]
            },
            "in_val": {
                "slot": "html_body",
                "slot_weight": 60,
                "name": "below"
            }
        },
        "page": {
            "block": "core/out/page",
            "x": 27,
            "y": 272,
            "in_con": {
                "enable": [
                    "router",
                    "skeleton"
                ]
            },
            "in_val": {
                "css_link": "/app/style/main.css"
            }
        },
        "main": {
            "block": "core/out/slot",
            "x": 441,
            "y": 88,
            "in_con": {
                "enable": [
                    "above",
                    "done"
                ],
                "slot": [
                    "above",
                    "name"
                ]
            },
            "in_val": {
                "slot_weight": 60,
                "name": "default"
            }
        },
        "above": {
            "block": "core/out/slot",
            "x": 219,
            "y": 205,
            "in_con": {
                "enable": [
                    "page",
                    "done"
                ]
            },
            "in_val": {
                "slot": "html_body",
                "slot_weight": 40,
                "name": "above"
            }
        }
    }
}