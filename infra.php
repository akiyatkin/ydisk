<?php
use akiyatkin\ydisk\Ydisk;
use infrajs\path\Path;
use infrajs\access\Access;

$conf = Ydisk::$conf;
if (!empty($conf['checkaccess'])) {
	Access::debug(true);
}

if (isset($_GET['-ydisk'])) {
	Ydisk::replace('~pages/','/pages/');
}