;<?php exit(); ?>
;
; Root index
;

[outputs]
done[]		= "index:done"


[block:index]
.block		= "gallery/index/load"

[block:show]
.block		= "gallery/index/show"
list[]		= "index:list"

; vim:filetype=dosini:

