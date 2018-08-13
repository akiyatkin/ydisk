<?php
use akiyatkin\ydisk\Ydisk;
use infrajs\path\Path;
use infrajs\access\Access;

if (isset($_GET['-ydisk'])) {
	Access::adminSetTime();
	Path::req('-ydisk/Ydisk.php');
	Ydisk::sync('~pages/','/pages/');
}