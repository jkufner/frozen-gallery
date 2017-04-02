<?php
/*
 * Copyright (c) 2011-2017, Josef Kufner  <jk@frozen-doe.net>
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

namespace Gallery\Gallery;

class Gallery
{

	public static function getGalleryInfo($dir)
	{
		if (is_dir($dir)) {
			$file = basename(rtrim($dir, '/'));
			if (preg_match_all('/^([0-9-]+)( ?[0-9]\+)?[ -.]+(.+)$/', $file, $matches)) {
				$date = $matches[2][0] != '' 
					? strftime('%Y-%m-%d %H:%M:%S', strtotime($matches[1][0]).' '.$matches[2][0])
					: strftime('%Y-%m-%d', strtotime($matches[1][0]));
				$title = str_replace('_', ' ', $matches[3][0]);
			} else {
				$date = null;
				$title = $file;
			}
			return array(
				'filename' => $file,
				'path' => $dir,
				'date' => $date,
				'title' => $title,
				'mtime' => strftime('%Y-%m-%d %H:%M:%S', filemtime($dir)),
			);
		} else {
			return false;
		}
	}


	public static function exifToLocation($exif)
	{
		if (empty($exif)) {
			return null;
		}
		if (isset($exif["GPSLongitude"]) && isset($exif['GPSLongitudeRef'])
			&& isset($exif["GPSLatitude"]) && isset($exif['GPSLatitudeRef']))
		{
			$lon = $this->exifCoordToDecimal($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
			$lat = $this->exifCoordToDecimal($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
			if (isset($exif['GPSAltitude'])) {
				$alt = $this->exifNumberToFloat($exif['GPSAltitude']);
			} else {
				$alt = null;
			}
		} else {
			$lon = null;
			$lat = null;
			$alt = null;
		}
		return array($lon, $lat, $alt);
	}


	public static function exifCoordToDecimal($coord, $ref)
	{
		$unit = 1;
		$val = 0;
		foreach ($coord as $c) {
			$val += $this->exifNumberToFloat($c) / $unit;
			$unit *= 60;
		}
		return ($ref == 'W' || $ref == 'S') ? - $val : $val;
	}


	public static function exifNumberToFloat($str)
	{
		@ list($a, $b) = explode('/', $str);
		if (!$b) {
			return $a ? $a : null;
		}
		return (float) $a / (float) $b;
	}

};


