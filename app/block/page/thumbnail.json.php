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
            "y": 63,
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
        "output": {
            "block": "core/out/output",
            "x": 216,
            "y": 0,
            "in_con": {
                "enable": [
                    "create",
                    "thumbnail_file"
                ],
                "filename": [
                    "create",
                    "thumbnail_file"
                ]
            },
            "in_val": {
                "template": "core/send_file",
                "slot": "root",
                "content_type": "image/jpeg",
                "expires": "now + 1 day"
            }
        }
    }
}
