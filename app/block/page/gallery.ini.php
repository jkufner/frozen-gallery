;<?php exit(); ?>
;
; Root index
;

[outputs]
title[]		= "gallery:title"
done[]		= "gallery:done"


[block:gallery]
.block		= "gallery/gallery/load"
directory[]	= "router:gallery"
subdirectory[]	= "router:path_tail"

[block:show]
.block		= "gallery/gallery/show"
list[]		= "gallery:list"
slot_weight	= 40

[block:others]
.block		= "core/out/menu"
items[]		= "gallery:others"
slot_weight	= 30

; vim:filetype=dosini:

