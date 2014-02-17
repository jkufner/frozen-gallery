{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "outputs": {
        "title": [
            "gallery:title"
        ],
        "done": [
            "gallery:done"
        ]
    },
    "blocks": {
        "gallery": {
            "block": "gallery/gallery/load",
            "x": 0,
            "y": 209,
            "in_con": {
                "gallery": [
                    "router",
                    "gallery"
                ],
                "path": [
                    "router",
                    "path_tail"
                ]
            }
        },
        "show": {
            "block": "gallery/gallery/show",
            "x": 267,
            "y": 209,
            "in_con": {
                "list": [
                    "gallery",
                    "list"
                ]
            },
            "in_val": {
                "slot_weight": 40
            }
        },
        "others": {
            "block": "core/out/menu",
            "x": 267,
            "y": 359,
            "in_con": {
                "items": [
                    "gallery",
                    "others"
                ]
            },
            "in_val": {
                "slot_weight": 30
            }
        },
        "path_header": {
            "block": "core/out/header",
            "x": 263,
            "y": 0,
            "in_val": {
                "level": 2,
                "slot_weight": 10
            },
            "in_con": {
                "text": [
                    "router",
                    "path"
                ]
            }
        }
    }
}