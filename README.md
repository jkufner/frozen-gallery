Gallery
=======

Requirements
------------

 * PHP
 * `exiftool` from `libimage-exiftool-perl` package
 
Installation
------------

  1. `composer install`
  2. Create `config.local-dev.yml` or `config.local-prod.yml` file. It can be
     empty or it can override anything from `app/config/config.yml`. — Don't
     copy the whole file, only what you need to change.
  3. Configure web server to send all requests to `index.php`.
  4. Put some directories with some photos to `data/gallery/` (or where the
     config file points to).
  5. Have fun.

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


