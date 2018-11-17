<?php
use infrajs\ans\Ans;
use infrajs\path\Path;
use infrajs\config\Config;
use infrajs\access\Access;
use akiyatkin\ydisk\Ydisk;
use infrajs\rest\Rest;


Rest::get( function () {
	echo '<a href="?-ydisk=true">Загрузить даннные с Яндекс Диска</a>';
});


