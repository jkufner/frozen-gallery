{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "outputs": {
        "title": "",
        "done": [
            "create:done"
        ]
    },
    "blocks": {
        "create": {
            "block": "gallery/photo/thumbnail",
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
                ],
                "size": [
                    "router",
                    "size"
                ]
            }
        },
        "show": {
            "block": "gallery/photo/show",
            "x": 239,
            "y": 0,
            "in_con": {
                "enable": [
                    "create",
                    "done"
                ],
                "image": [
                    "create",
                    "thumbnail"
                ]
            }
        }
    }
}