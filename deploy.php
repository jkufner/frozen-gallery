<?php
/*
 * Copyright (c) 2014, Josef Kufner  <jk@frozen-doe.net>
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

if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']
        && $_SERVER['REMOTE_ADDR'] != '127.0.0.1'
        && !preg_match('/192\.168\.[0-9]+\.[0-9]+/', $_SERVER['REMOTE_ADDR']))
{
        header('HTTP/1.0 403 Forbidden');
        echo 'Sorry, local access only.';
        exit();
}

header('Content-Type: text/plain; encoding=utf-8');

while (ob_get_level()) {
        ob_end_flush();
}

echo "Deploy:\n\n";

$f = fopen(__FILE__, "r");
flock($f, LOCK_EX);

// Make sure we have $HOME
$info = posix_getpwuid(posix_geteuid());
putenv("HOME=".$info['dir']);

// Deploy!
passthru("( git fetch -v origin master --tags && echo && git checkout origin/master --force && echo && composer install && echo && make doc ) 2>&1", $retval);

if ($retval == 0) {
        echo "\n\n# Success.";
}

flock($f, LOCK_UN);
fclose($f);

