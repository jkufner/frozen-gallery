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

namespace Gallery\Twig;

class BytesExtension extends \Twig_Extension
{

	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('bytes', array($this, 'bytesFilter')),
		);
	}

	public function bytesFilter($bytes)
	{
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$exp = max(0, min(floor(($bytes ? log($bytes) : 0) / log(1024)), count($units) - 1)); 
		$n = $bytes / pow(1024, $exp);
		return round($n, $n < 10 ? 1 : 0) . ' ' . $units[$exp];	// utf-8 nbsp.
	}

}

