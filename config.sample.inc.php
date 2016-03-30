<?php

/*  Copyright 2016, Tan Chee Eng

    This file is part of Simple Uploader with Plupload (SUP).

    SUP is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    SUP is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with SUP.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
 * Config file for the uploader app. Modify these settings as
 * necessary, then rename this file to "config.inc.php"
 *
 * Note: This app requires PHP 5.5 or higher and short tags enabled.
 */

/* The title that will be shown on the webpages */
define('TITLE', 'Simple Uploader with Plupload');

/* The chunk size in bytes */
define('CHUNK_SIZE', 4194304);

/* The URL to our directory, without the trailing slash */
define('BASE_URL', 'https://foobar.com/your/dir/here');

/* Upload destination directory. Must be writable by the server */
define('UPLOAD_DIR', '/path/to/destination/dir');

/*
 * List of usernames and passwords. Usernames in the key, and password hashes
 * in the values.
 *
 * Use hasher.php to generate hashes.
 */
global $users;
$users = array(
);
