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
            "in_con": {
                "size": [
                    "router",
                    "size"
                ],
                "filename": [
                    "router",
                    "path_tail"
                ]
            }
        },
        "show": {
            "block": "gallery/photo/show",
            "in_con": {
                "image": [
                    "create",
                    "thumbnail"
                ],
                "enable": [
                    "create",
                    "done"
                ]
            }
        }
    }
}