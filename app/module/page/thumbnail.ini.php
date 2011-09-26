;<?php exit(); ?>
;
; Thumbnail generator
;

[outputs]
title		= false
done[]		= "create:done"


[module:create]
.module		= "gallery/photo/thumbnail"
size[]		= "router:size"
filename[]	= "router:path_tail"

[module:show]
.module		= "gallery/photo/show"
image[]		= "create:thumbnail"
enable[]	= "create:done"



; vim:filetype=dosini:


