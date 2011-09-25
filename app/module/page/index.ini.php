;<?php exit(); ?>
;
; Root index
;

[outputs]
done[]		= "index:done"


[module:hd]
.module		= "core/out/header"
text[]		= "router:title"
level		= 1

[module:index]
.module		= "gallery/index/load"

[module:show]
.module		= "gallery/index/show"
list[]		= "index:list"

; vim:filetype=dosini:

