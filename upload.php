<?

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

require 'common.inc.php';

function retdie($success, $info = '') {
    die(json_encode(array(
        'OK' => $success,
        'info' => $info
    )));
}

// Returns true on success, false otherwise
function file_truncate($filename, $size) {
    $fh = @fopen($filename, "r+");
    if ($fh === false) return false;
    $ret = ftruncate($fh, $size);
    @fclose($fh);
    return $ret;
}

if (!authenticated()) {
    retdie(0, "Authentication token missing");
}

if (empty($_FILES) || $_FILES["file"]["error"]) {
    retdie(0, "Missing parameter");
}

$chunk = isset($_REQUEST['chunk']) ? intval($_REQUEST['chunk']) : false;
$chunks = isset($_REQUEST['chunks']) ? intval($_REQUEST['chunks']) : false;

// We only support chunk uploads
if ($chunks === false || $chunk === false || !isset($_REQUEST['name'])) {
    retdie(0, "Missing chunk info");
}

$srcfile = $_FILES['file']['tmp_name'];
$dstfile = dst_path($_REQUEST['name']);

// Read the uploaded chunk
$chunkdata = file_get_contents($srcfile, false);
if ($chunkdata === false) {
    retdie(0, "Could not read chunk");
}

if ($chunk == 0) {
    // Create tracking data
    if (!isset($_SESSION['uploads'])) $_SESSION['uploads'] = array();
    $_SESSION['uploads'][$dstfile] = -1;
    $meta = &$_SESSION['uploads'][$dstfile];

    // Open the file
    $fh = @fopen($dstfile.".part", 'wb');
} else {
    // Get the tracking data
    if (!isset($_SESSION['uploads'][$dstfile])) {
        retdie(0, "Missing chunk 0?");
    }
    $meta = &$_SESSION['uploads'][$dstfile];

    // Check that this chunk is the expected one
    if ($chunk != ($meta + 1)) {
        retdie(0, "Chunk out of order");
    }

    // Truncate the file, if necessary
    $fsize = @filesize($dstfile.".part");
    $chunkoffset = $chunk * CHUNK_SIZE;
    if ($fsize === false) {
        retdie(0, "Could not get file size");
    }
    if ($fsize > $chunkoffset) {
        // Might have been a retry, so truncate the file
        file_truncate($dstfile.".part", $chunkoffset);
    } else if ($fsize < $chunkoffset) {
        retdie(0, "Missing data in previous chunk or missing chunk");
    }

    // Open the file
    $fh = @fopen($dstfile.".part", "ab");
}

// Note: Meta contains the last successful chunk

if ($fh === false) {
    retdie(0, "Could not open file");
}

// Actually write
if (fwrite($fh, $chunkdata) === false) {
    retdie(0, "Could not write to file");
}

$meta = $chunk;
@fclose($fh);

// Is this the last chunk?
if ($chunk == ($chunks - 1)) {
    rename("${dstfile}.part", $dstfile);
}

retdie(1);
