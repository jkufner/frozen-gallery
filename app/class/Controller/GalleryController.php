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
	public function indexAction()
	{
		$gallery_config = $this->getParameter('gallery');

		$list = static::buildGalleryListing($gallery_config, '/');

		return $this->render('index.html.twig', [
			'title' => $gallery_config['name'],
			'breadcrumbs' => $this->buildBreadcrumbs($gallery_config, null, '/'),
			'list' => $list,
		]);
	}


	public function galleryAction($gallery, $path = "/")
	{
		$gallery_config = $this->getParameter('gallery');

		$path_prefix = $gallery_config['path_prefix'];

		$list = array();
		$others = array();

		$gallery_info = Gallery::getGalleryInfo($path_prefix.str_replace('/', '_', $gallery));

                $filename = rtrim($gallery_info['path'], '/').'/'.rtrim($path, '/');

		if ($gallery_info === false) {
			throw $this->createNotFoundException('Gallery not found.');
		} else if (is_dir($filename)) {
			return $this->handleDirectory($gallery_config, $gallery_info, $path, $filename);
		} else {
			$exiftool_json = '/_exiftool.json';
			if (substr_compare($path, $gallery_config['url_thumbnail_ext'], - strlen($gallery_config['url_thumbnail_ext'])) === 0) {
				$src_filename = substr($filename, 0, - strlen($gallery_config['url_thumbnail_ext']));
				if (is_file($src_filename)) {
					return $this->handleThumbnail($gallery_config, $gallery_info, $path, $filename, $src_filename);
				} else {
					return $this->handleFile($gallery_config, $gallery_info, $path, $filename);
				}
			} else {
				return $this->handleFile($gallery_config, $gallery_info, $path, $filename);
			}
		}

	}


	protected function buildBreadcrumbs($gallery_config, $gallery_info, $path)
	{
		$path_parts = $path == '/' ? array() : explode('/', trim($path, '/'));
		$breadcrumbs = array();

		$prefix = $gallery_info ? $gallery_config['url_prefix'].'/'.$gallery_info['filename'].'/' : $gallery_config['url_prefix'].'/';

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

		$breadcrumbs[] = [
			'label' => $gallery_config['root_breadcrumb'],
			'url' => $gallery_config['url_prefix'],
		];

		return array_reverse($breadcrumbs);
	}


	protected function buildGalleryListing($gallery_config, $path)
	{
		$path_prefix = rtrim($gallery_config['path_prefix'], '/').$path;
		$url_prefix  = rtrim($gallery_config['url_prefix'], '/').$path;
		$index_file  = $gallery_config['index_file'];
		$list = array();

		if ($index_file) {
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


	protected function handleDirectory($gallery_config, $gallery_info, $path, $filename)
	{
		$path_prefix = $gallery_config['path_prefix'].'/';
		$url_prefix  = $gallery_config['url_prefix'].$gallery_info['filename'].'/';
		$url_thumbnail_ext = $gallery_config['url_thumbnail_ext'];

		$d = opendir($filename);
		$list = [];
		$others = [];

		$exif_data = $this->loadExiftoolJson($filename);
		//var_dump($exif_data);

		$show_map = false;

		while (($file = readdir($d)) !== false) {
			if ($file[0] != '.') {
				$full_name = $filename.'/'.$file;
				$path_url = $path == '/' ? $url_prefix : $url_prefix.$path.'/';
				$file_url = $path_url.$file;

				if (!preg_match('/(\.jpe?g|\.png|\.gif|\.tiff)$/i', $file)) {
					$img = null;
				} else if (isset($exif_data[$file])) {
					$img = $exif_data[$file];
				} else {
					$img = $this->readImageMetadata($full_name);
				}

				if ($img && isset($img['width']) && isset($img['height'])) {
					list($tb_width, $tb_height) = Thumbnail::calculateThumbnailSize($img['width'], $img['height'], $img['orientation'],
						$gallery_config['resize_mode'], $gallery_config['thumbnail_size'], $gallery_config['thumbnail_size']);

					// Store image
					$img['filename'] = $file;
					$img['full_name'] = $full_name;
					$img['url'] = $file_url;
					$img['tb_url'] = $file_url.$url_thumbnail_ext;
					$img['tb_width'] = $tb_width;
					$img['tb_height'] = $tb_height;
					$list[$file] = $img;

					$show_map |= isset($img['location']['lat']) && isset($img['location']['lng']);
				} else {
					// Store generic file
					$others[$file] = array(
						'title' => $file,
						'link' => $file_url,
						'size' => is_file($full_name) ? filesize($full_name) : null,
					);
				}
			}
		}

		closedir($d);

		uksort($list, 'strcoll');
		uksort($others, 'strcoll');

		$dav_url = $gallery_config['dav_url_prefix']
			.str_replace('%2F', '/', rawurlencode($gallery_info['filename'].($path == '/' ? '/' : '/'.$path.'/')));

		return $this->render('gallery.html.twig', [
			'title' => $gallery_info['title'],
			'date' => $gallery_info['date'] ? $gallery_info['date'] : null,
			'breadcrumbs' => $this->buildBreadcrumbs($gallery_config, $gallery_info, $path),
			'show_map' => $show_map,
			'info' => $gallery_info,
			'list' => $list,
			'others' => $others,
			'dav_url' => empty($gallery_config['dav_url_prefix']) ? null : $dav_url,
			'exiftool_json' => ($path == '/' ? $url_prefix : $url_prefix.$path.'/').'_exiftool.json',
		]);
	}


	protected function handleFile($gallery_config, $gallery_info, $path, $filename)
	{
                if (file_exists($filename)) {
			return new BinaryFileResponse($filename);
		} else {
			throw $this->createNotFoundException('File not found: '.$filename);
		}
	}


	protected function handleThumbnail($gallery_config, $gallery_info, $path, $filename, $src_filename, $size = -1)
	{
		$path_prefix = $gallery_config['path_prefix'];
		$url_prefix  = $gallery_config['url_prefix'];
		$url_thumbnail_ext = $gallery_config['url_thumbnail_ext'];

		if ($size <= 0) {
			$size = $gallery_config['thumbnail_size'];
		}

                // prepare cache file
                $cache_dir = 'var/cache';
                if (!is_dir($cache_dir)) {
                        mkdir($cache_dir);
                }
                $cache_fn = md5($src_filename.'|'.$size.'|'.$gallery_config['resize_mode']);
                $cache_file = $cache_dir.'/'.substr($cache_fn, 0, 2);
                if (!is_dir($cache_file)) {
                        mkdir($cache_file);
                }
                $cache_file .= '/'.$cache_fn;

                // update cache if required
		if (!is_readable($cache_file) || filemtime($src_filename) > filemtime($cache_file) /* || filemtime(__FILE__) > filemtime($cache_file) */) {
                        Thumbnail::generateThumbnail($cache_file, $src_filename, $size, $size, $gallery_config['resize_mode']);
                }
                if ($cache_file !== false) {
                        //$this->out('thumbnail_file', $cache_file);
			return new BinaryFileResponse($cache_file);
		} else {
			throw $this->createNotFoundException('Thumbnail not found.');
		}
	}


	public function handleExiftool($gallery_config, $gallery_info, $path, $filename)
	{
		// passthru('cd '.escapeshellarg(dirname($filename)).'; exiftool -j -SourceFile -GPSLatitude -GPSLongitude .');
		passthru('cd '.escapeshellarg(dirname($filename)).'; exiftool -j -SourceFile -GPSLatitude -GPSLongitude .');
		die();
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
			foreach (json_decode($p->getOutput(), TRUE) as $f) {
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
		$exif = @ exif_read_data($full_name, 0, TRUE);
		if ($exif) {
			$location = Gallery::exifToLocation($exif);
			if (isset($exif['COMPUTED']['Width']) && isset($exif['COMPUTED']['Height'])) {
				$width = $exif['COMPUTED']['Width'];
				$height = $exif['COMPUTED']['Height'];
			} else {
				@ list($width, $height) = getimagesize($full_name);
			}
			$orientation = isset($exif['IFD0']['Orientation']) ? $exif['IFD0']['Orientation'] : 0;
		} else {
			$location = null;
			$size = getimagesize($full_name);
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

