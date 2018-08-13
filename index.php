<?php
use infrajs\ans\Ans;
use infrajs\path\Path;
use akiyatkin\ydisk\Ydisk;


Ydisk::sync('~pages/','/pages/');

echo 'Синхронизация выполнена '.date('H:i');