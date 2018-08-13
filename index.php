<?php
use infrajs\ans\Ans;
use infrajs\path\Path;
use akiyatkin\ydisk\Ydisk;

Path::req('-ydisk/Ydisk.php');

Ydisk::sync('~pages/','/pages/');

echo 'Синхронизация выполнена '.date('H:i');