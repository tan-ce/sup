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

if (isset($_POST['pwd']) && isset($_POST['pwd2'])) {
    if ($_POST['pwd'] != $_POST['pwd2']) {
        echo 'No match';
        return;
    }
    echo password_hash($_POST['pwd'], PASSWORD_BCRYPT, array('cost' => '14'));
    return;
}
?><html><head><title>Hash Generator</title></head><body>
<form method="post" action="hasher.php">
    <input type="password" name="pwd" /><br />
    <input type="password" name="pwd2" /><br />
    <input type="submit" value="Generate Hash" />
</form>
</body></html>
