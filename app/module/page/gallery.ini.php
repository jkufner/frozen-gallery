;<?php exit(); ?>
;
; Root index
;

[outputs]
title[]		= "gallery:title"
done[]		= "gallery:done"


[module:gallery]
.module		= "gallery/gallery/load"
directory[]	= "router:gallery"

[module:show]
.module		= "gallery/gallery/show"
list[]		= "gallery:list"
slot-weight	= 40

[module:others]
.module		= "core/out/menu"
items[]		= "gallery:others"
slot-weight	= 30

; vim:filetype=dosini:

