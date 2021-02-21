<?php
/*
 * Copyright (c) 2017, Josef Kufner  <josef@kufner.cz>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Gallery\Controller;

use Gallery\Gallery\Gallery;
use Gallery\Gallery\Thumbnail;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;


/**
 * Gallery controller
 */
class GalleryController extends Controller
{

	/**
	 * Directory index
	 */
	public function indexAction(?string $list = null)
	{
		$list = $this->buildGalleryListing('/', $list);

		return $this->render('index.html.twig', [
			'title' => $this->getParameter('gallery.name'),
			'breadcrumbs' => $this->buildBreadcrumbs(null, '/'),
			'date_format' => $this->getParameter('gallery.date_format'),
			'datetime_format' => $this->getParameter('gallery.datetime_format'),
			'list' => $list,
		]);
	}


	public function galleryAction($gallery, $path = "/")
	{
		$path_prefix = $this->getParameter('gallery.path_prefix');

		$list = array();
		$others = array();

		$gallery_info = Gallery::getGalleryInfo($path_prefix.str_replace('/', '_', $gallery));

		if (!$gallery_info) {
			throw new NotFoundHttpException('Gallery not found: ' . $gallery);
		}

		$filename = rtrim($gallery_info['path'], '/').'/'.rtrim($path, '/');

		if ($gallery_info === false) {
			throw $this->createNotFoundException('Gallery not found.');
		} else if (is_dir($filename)) {
			return $this->handleDirectory($gallery_info, $path, $filename);
		} else {
			// Try thumbnail
			$url_thumbnail_ext = $this->getParameter('gallery.url_thumbnail_ext');
			if (substr_compare($path, $url_thumbnail_ext, - strlen($url_thumbnail_ext)) === 0) {
				$src_filename = substr($filename, 0, - strlen($url_thumbnail_ext));
				if (is_file($src_filename)) {
					return $this->handleThumbnail($gallery_info, $path, $filename, $src_filename);
				}
			}

			// Try preview
			$url_preview_ext = $this->getParameter('gallery.url_preview_ext');
			if (substr_compare($path, $url_preview_ext, - strlen($url_preview_ext)) === 0) {
				$src_filename = substr($filename, 0, - strlen($url_preview_ext));
				if (is_file($src_filename)) {
					return $this->handlePreview($gallery_info, $path, $filename, $src_filename);
				}
			}

			// Otherwise serve file
			return $this->handleFile($gallery_info, $path, $filename);
		}

	}


	protected function buildBreadcrumbs($gallery_info, $path)
	{
		$path_parts = $path == '/' ? array() : explode('/', trim($path, '/'));
		$breadcrumbs = array();

		$url_prefix = $this->getParameter('gallery.url_prefix');
		$prefix = $gallery_info ? rtrim($url_prefix, '/').'/'.$gallery_info['filename'].'/' : $url_prefix.'/';

		for ($i = count($path_parts); $i > 0; $i--) {
			$breadcrumbs[] = array(
				'label' => end($path_parts),
				'url' => $prefix.join('/', $path_parts),
			);
			array_pop($path_parts);
		}

		if ($gallery_info) {
			$breadcrumbs[] = [
				'label' => $gallery_info['title'],
				'url' => '/'.$gallery_info['filename'],
			];
		}

		$root_breadcrumb = $this->getParameter('gallery.root_breadcrumb');
		if ($root_breadcrumb !== null) {
			$breadcrumbs[] = [
				'label' => $root_breadcrumb === "" ? $this->getParameter('gallery.name') : $root_breadcrumb,
				'url' => $url_prefix,
			];
		}

		return array_reverse($breadcrumbs);
	}


	protected function buildGalleryListing($path, ?string $list = null)
	{
		$path_prefix = rtrim($this->getParameter('gallery.path_prefix'), '/').$path;
		$url_prefix  = rtrim($this->getParameter('gallery.url_prefix'), '/').$path;
		if ($list !== null) {
			$index_file = dirname($this->getParameter('gallery.index_file')) . '/' . $list . '.list';
		} else {
			$index_file = $this->getParameter('gallery.index_file');
		}
		$list = array();

		if ($index_file) {
			if (!file_exists($index_file)) {
				throw new NotFoundHttpException('Gallery listing not found.');
			}

			// Read index file and scan only named subdirectories
			foreach (file($index_file) as $file) {
				$file = trim($file);
				if ($file == '' || $file[0] == '#') {
					continue;
				}
				if (is_dir($path_prefix.$file)) {
					$info = Gallery::getGalleryInfo($path_prefix.$file);
					if ($info !== false) {
						$list[$file] = array_merge($info, array(
								'url' => $url_prefix.$file,
							));
					}
				}
			}
		} else if (($d = opendir($path_prefix))) {
			// Read directory contents and scan all subdirecotries
			while (($file = readdir($d)) !== false) {
				if ($file[0] != '.' && is_dir($path_prefix.$file)) {
					$info = Gallery::getGalleryInfo($path_prefix.$file);
					if ($info !== false) {
						$list[$file] = array_merge($info, array(
								'url' => $url_prefix.$file,
							));
					}
				}
			}

			closedir($d);
		}

		uksort($list, 'strcoll');

		return $list;
	}


	protected function handleDirectory($gallery_info, $path, $filename)
	{
		// Load directory content
		list($images, $others, $have_geo_data) = $this->loadDirectory($filename);

		// Calculate size of each thumbnail
		$tb_size = $this->getParameter('gallery.thumbnail_size');
		$tb_resize_mode = $this->getParameter('gallery.resize_mode');
		foreach ($images as $f => $img) {
			list($images[$f]['tb_width'], $images[$f]['tb_height']) = Thumbnail::calculateThumbnailSize($img['width'], $img['height'],
				$img['orientation'], $tb_resize_mode, $tb_size, $tb_size); 
		}

		// Calculate WebDAV URL
		$dav_url_prefix = $this->getParameter('gallery.dav_url_prefix');
		$dav_url = $dav_url_prefix ? $dav_url_prefix.str_replace('%2F', '/', rawurlencode($gallery_info['filename'].($path == '/' ? '/' : '/'.$path.'/'))) : null;

		return $this->render('gallery.html.twig', [
			'title' => $gallery_info['title'],
			'date' => $gallery_info['date'] ? $gallery_info['date'] : null,
			'breadcrumbs' => $this->buildBreadcrumbs($gallery_info, $path),
			'url_prefix' => $this->getParameter('gallery.url_prefix').$gallery_info['filename'].($path == '/' ? '/' : '/'.$path.'/'),
			'tb_suffix' => $this->getParameter('gallery.url_thumbnail_ext'),
			'preview_suffix' => $this->getParameter('gallery.url_preview_ext'),
			'images' => $images,
			'others' => $others,
			'show_map' => $have_geo_data,
			'dav_url' => $dav_url,
			'date_format' => $this->getParameter('gallery.date_format'),
			'datetime_format' => $this->getParameter('gallery.datetime_format'),
		]);
	}


	protected function handleFile($gallery_info, $path, $filename)
	{
		if (file_exists($filename)) {
			return new BinaryFileResponse($filename);
		} else {
			throw $this->createNotFoundException('File not found: '.$filename);
		}
	}


	protected function handleThumbnail($gallery_info, $path, $filename, $src_filename, $size = -1, $orig_max_size_bytes = -1)
	{
		$resize_mode = $this->getParameter('gallery.resize_mode');

		if ($size <= 0) {
			$size = $this->getParameter('gallery.thumbnail_size');
		}

		// prepare cache file
		$cache_dir = $this->getParameter('gallery.thumbnail_cache_path');
		if (!is_dir($cache_dir)) {
			mkdir($cache_dir);
		}
		$cache_fn = md5($src_filename.'|'.$size.'|'.$resize_mode);
		$cache_file = $cache_dir.'/'.substr($cache_fn, 0, 2);
		if (!is_dir($cache_file)) {
			mkdir($cache_file);
		}
		$cache_file .= '/'.$cache_fn;

		// Use original, if it is small enough
		if ($orig_max_size_bytes > 0 && (preg_match('/\.gif$/i', $src_filename) || filesize($src_filename) <= $orig_max_size_bytes)) {
			return new BinaryFileResponse($src_filename);
		}

		// update cache if required
		if (!is_readable($cache_file) || filemtime($src_filename) > filemtime($cache_file) /* || filemtime(__FILE__) > filemtime($cache_file) */) {
			Thumbnail::generateThumbnail($cache_file, $src_filename, $size, $size, $resize_mode);
		}
		if ($cache_file !== false) {
			return new BinaryFileResponse($cache_file);
		} else {
			throw $this->createNotFoundException('Thumbnail not found.');
		}
	}


	protected function handlePreview($gallery_info, $path, $filename, $src_filename)
	{
		return $this->handleThumbnail($gallery_info, $path, $filename, $src_filename,
			$this->getParameter('gallery.preview_size'),
			$this->getParameter('gallery.preview_min_bytes'));
	}


	protected function loadDirectory($dirname)
	{
		// Result storage
		$list = [];
		$others = [];
		$have_geo_data = false;

		// Load directory
		$files = [];
		$latest_mtime = max(filemtime($dirname), filemtime(__FILE__));
		$d = opendir($dirname);
		while (($filename = readdir($d)) !== false) {
			if ($filename[0] == '.') {
				continue;
			}

			$files[] = $filename;

			// Get latest mtime
			$fn = $dirname.'/'.$filename;
			$mtime = filemtime($fn);
			if ($mtime > $latest_mtime) {
				$latest_mtime = $mtime;
			}
		}
		closedir($d);

		// Check cache
		$cache = $this->get('cache.app');
		$cache_key = md5('gallery:'.$dirname);
		$cache_item = $cache->getItem($cache_key);
		if ($cache_item->isHit()) {
			list($cache_mtime, $cache_data) = $cache_item->get();
			list($cache_data_list, $cache_data_others, $cache_data_have_geo_data) = $cache_data;
			if ($cache_mtime >= $latest_mtime && count($files) == count($cache_data_list) + count($cache_data_others)) {
				return $cache_data;
			}
		}

		// Load EXIF for all files in the directory
		$exif_data = $this->loadExiftoolJson($dirname);

		// Build result sets
		foreach ($files as $filename) {
			$full_filename = $dirname.'/'.$filename;

			if (!preg_match('/(\.jpe?g|\.png|\.gif|\.tiff)$/i', $filename)) {
				$img = null;
			} else if (isset($exif_data[$filename])) {
				$img = $exif_data[$filename];
			} else {
				$img = $this->readImageMetadata($full_filename);
			}

			if ($img && isset($img['width']) && isset($img['height'])) {
				// Store image
				$img['filename'] = $filename;
				$img['size'] = filesize($full_filename);
				$list[$filename] = $img;

				// Got geo data?
				$have_geo_data |= isset($img['location']['lat']) && isset($img['location']['lng']);
			} else {
				// Store generic file
				$others[$filename] = array(
					'filename' => $filename,
					'size' => is_file($full_filename) ? filesize($full_filename) : null,
				);

				$have_geo_data |= preg_match('/\.gpx$/i', $filename);
			}
		}

		// Sort result
		uksort($list, 'strcoll');
		uksort($others, 'strcoll');

		$result = [ $list, $others, $have_geo_data ];

		// Update cache
		$cache_item->set([$latest_mtime, $result]);
		$cache->save($cache_item);

		return $result;
	}


	/**
	 * Run `exiftool -j -SourceFile -GPSLatitude -GPSLongitude .`
	 * on gallery directory to obtain EXIF metadata.
	 */
	protected function loadExiftoolJson($path = '.')
	{
		try {
			$pb = new ProcessBuilder(['exiftool', '-n', '-json', '-ImageWidth', '-ImageHeight', '-Orientation',
				'-SourceFile', '-GPSLatitude', '-GPSLongitude', $path]);
			$p = $pb->getProcess();
			//var_dump($p->getCommandLine());
			$p->run();

			$file_data = [];
			$exiftool_json = json_decode($p->getOutput(), TRUE);
			if (!is_array($exiftool_json)) {
				return [];
			}
			foreach ($exiftool_json as $f) {
				$fn = basename($f['SourceFile']);
				$file_data[$fn] = [
					'width' => isset($f['ImageWidth']) ? (int) $f['ImageWidth'] : null,
					'height' => isset($f['ImageHeight']) ? (int) $f['ImageHeight'] : null,
					'orientation' => isset($f['Orientation']) ? (int) $f['Orientation'] : null,
					'location' => [
						'lat' => isset($f['GPSLatitude'])  ? (float) $f['GPSLatitude'] : null,
						'lng' => isset($f['GPSLongitude']) ? (float) $f['GPSLongitude'] : null,
						'alt' => isset($f['GPSAltitude'])  ? (float) $f['GPSAltitude'] : null,
					],
				];
			}
		}
		catch (ProcessFailedException $ex) {
			throw $ex;
		}

		return $file_data;
	}

	protected function readImageMetadata($filename)
	{
		// get metadata
		$exif = @ exif_read_data($filename, 0, TRUE);
		if ($exif) {
			$location = Gallery::exifToLocation($exif);
			if (isset($exif['COMPUTED']['Width']) && isset($exif['COMPUTED']['Height'])) {
				$width = $exif['COMPUTED']['Width'];
				$height = $exif['COMPUTED']['Height'];
			} else {
				@ list($width, $height) = getimagesize($filename);
			}
			$orientation = isset($exif['IFD0']['Orientation']) ? $exif['IFD0']['Orientation'] : 0;
		} else {
			$location = null;
			$size = getimagesize($filename);
			$orientation = 0;
		}

		return [
			'width' => $size[0],
			'height' => $size[1],
			'orientation' => $orientation,
			'location' => $location,
		];
	}

}

