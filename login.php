<? require 'common.inc.php';

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

if (isset($_GET['logout'])) {
    auth_clear();
}

if (isset($_POST['name']) && isset($_POST['pwd'])) {
    $invalid = true;
    auth_check($_POST['name'], $_POST['pwd']);
} else {
    $invalid = false;
}

if (authenticated()) rdrdie('index.php');

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=TITLE?></title>
    <link rel="stylesheet" href="<?=url('style.css')?>" media="screen">
</head>
<body><div id="wrap">
<? if ($invalid) { ?><div>
    That username or password was invalid!
</div><br /><? } ?>
<form method="post" action="<?=url('login.php')?>">
    <input type="text" name="name" placeholder="User" /><br />
    <input type="password" name="pwd" placeholder="Password" /><br />
    <input type="submit" value="Login" />
</form>
</div></body></html>
