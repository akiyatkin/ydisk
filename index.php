<?php
use infrajs\ans\Ans;
use infrajs\path\Path;
use infrajs\config\Config;
use infrajs\access\Access;
use akiyatkin\ydisk\Ydisk;
use infrajs\rest\Rest;

Access::debug(true);

Rest::get( function () {
	$ans = array('sync' => Ydisk::$conf['sync']);
	$html = Rest::parse('-ydisk/layout.tpl',$ans);
	echo $html;
});


