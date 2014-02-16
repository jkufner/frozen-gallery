{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "outputs": {
        "done": [
            "index:done"
        ]
    },
    "blocks": {
        "index": {
            "block": "gallery/index/load"
        },
        "show": {
            "block": "gallery/index/show",
            "in_con": {
                "list": [
                    "index",
                    "list"
                ]
            }
        }
    }
}