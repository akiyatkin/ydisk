<?php
use akiyatkin\ydisk\Ydisk;
use infrajs\path\Path;
use infrajs\ans\Ans;
use infrajs\access\Access;
use infrajs\nostore\Nostore;
use infrajs\view\View;

$conf = Ydisk::$conf;
if (!empty($conf['checkaccess'])) {
	Access::debug(true);
}
$name = Ans::get('-ydisk');
if ($name === 'true') {
	Nostore::on();
	Ydisk::replaceAll();
}
if (isset($conf['sync'][$name])) {
	Nostore::on();
	$rule = $conf['sync'][$name];
	Ydisk::replace($rule['site'],$rule['ysrc']);	
}
if ($name) {
	Nostore::on();
	$path = explode('?',$_SERVER['REQUEST_URI'])[0];
	header('Location: '.$path.'?-nostore=true');
}