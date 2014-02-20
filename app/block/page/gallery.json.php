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
            "y": 0,
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
            "y": 0,
            "in_con": {
                "list": [
                    "gallery",
                    "list"
                ]
            },
            "in_val": {
                "slot": "below",
                "slot_weight": 40
            }
        },
        "others": {
            "block": "core/out/menu",
            "x": 267,
            "y": 150,
            "in_con": {
                "items": [
                    "gallery",
                    "others"
                ]
            },
            "in_val": {
                "slot_weight": 30
            }
        }
    }
}