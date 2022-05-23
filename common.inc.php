<?php require 'config.inc.php';

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

session_start();

// Generate absolute URL from relative
function url($path) {
    return BASE_URL . "/${path}";
}

// Create absolute path to destination file
function dst_path($filename) {
    return UPLOAD_DIR . "/" .
        preg_replace('/[^a-zA-Z0-9-_\.\(\)]/', '_', $filename);
}

// Redirect to local destination then die
function rdrdie($dst, $code = 303) {
    header("Location: " . url($dst), true, $code);
    exit;
}

// Session is authenticated?
function authenticated() {
    return isset($_SESSION['auth']) && $_SESSION['auth'] == 'OK';
}

function get_curl_opt($ch, $cfg) {
    $cfg_src = 'NGINX_AUTH_'.$cfg;
    if (!defined($cfg_src)) return;
    $val = constant($cfg_src);
    if (!curl_setopt($ch, constant('CURLOPT_'.$cfg), $val)) {
        echo "Authentication: Failed to set CURLOPT_$cfg";
        exit;
    }
}

/*
 * Check the username and password.
 *
 * If valid, set the session marker and return true.
 * If invalid, return false
 */
function auth_check($name, $pwd) {
    if (defined('NGINX_AUTH_URL')) {
        $ch = curl_init(NGINX_AUTH_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_USERPWD, $name.":".$pwd);
        get_curl_opt($ch, 'CAINFO');
        get_curl_opt($ch, 'SSLCERT');
        get_curl_opt($ch, 'SSLKEY');
        get_curl_opt($ch, 'SSLCERTPASSWD');
        $output = curl_exec($ch);
        if ($output === false) {
            echo "Authentication error: ".curl_error($ch);
            exit;
        }
        curl_close($ch);

        if ($output == "OK") {
            $_SESSION['auth'] = 'OK';
            return true;
        } else {
            return false;
        }
    } else {
        global $users;

        if (!isset($users[$name])) return false;
        $ret = password_verify($pwd, $users[$name]);
        if ($ret) $_SESSION['auth'] = 'OK';
        return $ret;
    }
}

// Logout
function auth_clear() {
    unset($_SESSION['auth']);
    session_destroy();
}
