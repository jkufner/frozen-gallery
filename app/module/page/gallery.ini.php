;<?php exit(); ?>
;
; Root index
;

[outputs]
title[]		= "gallery:title"
done[]		= "gallery:done"


[module:gallery]
.module		= "gallery/gallery/load"
directory[]	= "router:path"

[module:show]
.module		= "gallery/gallery/show"
list[]		= "gallery:list"

; vim:filetype=dosini:

