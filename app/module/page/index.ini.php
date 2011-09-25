;<?php exit(); ?>
;
; Root index
;

[outputs]
done[]		= "index:done"


[module:index]
.module		= "gallery/index/load"

[module:show]
.module		= "gallery/index/show"
list[]		= "index:list"

; vim:filetype=dosini:

