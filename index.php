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
if (!authenticated()) rdrdie('login.php');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=TITLE?></title>
    <link rel="stylesheet" href="<?=url('style.css')?>" media="screen">
    <script src="<?=url('jquery-2.2.2.min.js')?>"></script>
    <script src="<?=url('plupload/js/moxie.min.js')?>"></script>
    <script src="<?=url('plupload/js/plupload.min.js')?>"></script>
    <script type="text/javascript">
        function onedp(val) {
            return parseFloat(Math.round(val * 10) / 10).toFixed(2);
        }

        function pretty_speed(speed) {
            if (speed < 1024) {
                return speed + " B/s";
            } else speed /= 1024;

            if (speed < 1024) {
                return onedp(speed) + " KiB/s";
            } else speed /= 1024;

            return onedp(speed) + " MiB/s";
        }

        function msgout(msg) {
            document.getElementById('console').innerHTML +=
                "\n[" + (new Date()).toLocaleString() + "] " +  msg;
        }

        $(document).ready(function() {
            var uploader = new plupload.Uploader({
                runtimes: 'html5,html4',
                chunk_size: <? printf("%d", CHUNK_SIZE); ?>,
                max_retries: 5,
                browse_button: 'browse',
                url: '<?=url('upload.php')?>'
            });

            uploader.init();

            uploader.bind('FilesAdded', function(up, files) {
                var html = '';
                plupload.each(files, function(file) {
                    html += '<li id="' + file.id + '"><b></b> ' +
                            file.name + ' (' +
                            plupload.formatSize(file.size) + ')</li>';
                });
                document.getElementById('filelist').innerHTML += html;
            });

            var last_loaded;
            var last_ts;
            var start_ts = null;
            var total_sent = 0;

            uploader.bind('BeforeUpload', function(up, file) {
                last_ts = (new Date).getTime();
                last_loaded = 0;
                if (start_ts == null) start_ts = last_ts;
                msgout('Starting upload of ' + file.name);
            });

            uploader.bind('UploadProgress', function(up, file) {
                document.getElementById(file.id)
                    .getElementsByTagName('b')[0].innerHTML =
                        '<span>[ ' + file.percent + '% ]</span>';

                // Calculate speed
                var speed;
                var cur_ts = (new Date).getTime();
                var elapsed = cur_ts - last_ts;
                if (elapsed >= 500) {
                    last_ts = cur_ts;
                    var bytes_sent = file.loaded - last_loaded;
                    last_loaded = file.loaded;
                    speed = bytes_sent * 1000 / elapsed;

                    total_sent += bytes_sent;
                    avg_speed = total_sent * 1000 / (cur_ts - start_ts);

                    document.getElementById('speed').innerHTML =
                        "Speed = " + pretty_speed(speed) + ", Average Speed = " +
                        pretty_speed(avg_speed);
                }
            });

            uploader.bind('ChunkUploaded', function(up, file, info) {
                var resp = $.parseJSON(info.response);
                if (resp.OK == 0) {
                    msgout("Chunk Error: " + resp.info);
                    up.stop();
                }
            });

            uploader.bind('FileUploaded', function(up, file, info) {
                var resp = $.parseJSON(info.response);
                if (resp.OK == 0) {
                    msgout("File Upload Error: " + resp.info);
                    up.stop();
                } else {
                    msgout(file.name + " uploaded successfully");
                }

            });

            uploader.bind('Error', function(up, err) {
                msgout("Error #" + err.code + ": " + err.message);
            });

            document.getElementById('start-upload').onclick = function() {
                uploader.start();
            };
        });
    </script>
</head>
<body><div id="wrap">
<a id="logout" href="<?=url('login.php?logout=1')?>">Logout</a>
<h1><?=TITLE?></h1>
<ul id="filelist"></ul>
<div id="speed"></div><br />

<div id="container">
    <a class="button" id="browse" href="javascript:;">Browse...</a>
    <a class="button" id="start-upload" href="javascript:;">Start Upload</a>
</div>

<pre id="console"></pre>
</div></body></html>
