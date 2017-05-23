Frozen Gallery
==============

Requirements
------------

 * PHP
 * `exiftool` from `libimage-exiftool-perl` package
 
Installation
------------

  1. Download gallery.
  2. `composer install`
  3. Create `config.local-dev.yml` or `config.local-prod.yml` file. It can be
     empty or it can override anything from `app/config/config.yml`. — Don't
     copy the whole file, only what you need to change.
  4. Create `var` directory, make sure it is writable by your webserver.
  5. Configure web server to send all requests to `index.php`.
  6. Put some directories with some photos to `data/gallery/` (or where the
     config file points to).
  7. Have fun.

Gallery Structure
-----------------

    data/gallery
     |-- YYYY-MM-DD  Some gallery
     |    |-- photo1.jpg
     |    `-- photo2.jpg
     `-- index.list

  - Each directory is a gallery.
  - `index.list` contains list of visible galleries (directory names; one per
    line). Galleries not listed in the `index.list` are still fully accessible,
    but visitors need to know its URL.
  - If you have trouble with obsolete gallery content, just update the
    directory's mtime using `touch directory` (or any other way).


