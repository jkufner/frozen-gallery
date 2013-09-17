;<?php exit(); ?>
;
; Thumbnail generator
;

[outputs]
title		= false
done[]		= "create:done"


[block:create]
.block		= "gallery/photo/thumbnail"
size[]		= "router:size"
filename[]	= "router:path_tail"

[block:show]
.block		= "gallery/photo/show"
image[]		= "create:thumbnail"
enable[]	= "create:done"



; vim:filetype=dosini:


