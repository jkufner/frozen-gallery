; <?php exit(); ?>
;
; Default routes for 'hello world' and documentation browser
;
; Look at core/value/pipeline_loader module before you use this.
;

[#]
title = "Gallery"
title_fmt = "%s - gallery.frozen-doe.net" 
skeleton = true

[/]
content = "page/index"
title = "Index of gallery.frozen-doe.net"
title_fmt = "%s"

[/version]
content = "page/version"
title = "Version"

[/profiler]
content = "page/profiler"
title = "Profiler Statistics"

[/thumbnail/**]
content = "page/thumbnail"
size = 120
skeleton = false

[/preview/**]
content = "page/thumbnail"
size = 800
skeleton = false

[/$gallery]
content = "page/gallery"

[/$gallery/**]
content = "page/gallery"

; vim:filetype=dosini:

