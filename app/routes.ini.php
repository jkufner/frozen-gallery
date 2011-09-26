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

[/$gallery]
content = "page/gallery"

[/$gallery/$photo]
content = "page/gallery"

[/thumbnail/**]
content = "page/thumbnail"
size = 120

[/preview/**]
content = "page/thumbnail"
size = 800


; vim:filetype=dosini:

