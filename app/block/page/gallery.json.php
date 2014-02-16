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
            "in_con": {
                "directory": [
                    "router",
                    "gallery"
                ],
                "subdirectory": [
                    "router",
                    "path_tail"
                ]
            }
        },
        "show": {
            "block": "gallery/gallery/show",
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