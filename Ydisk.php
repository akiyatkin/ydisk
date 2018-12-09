<?php
namespace akiyatkin\ydisk;
use infrajs\ans\Ans;
use Yandex\Disk\DiskClient;
use infrajs\once\Once;
use infrajs\path\Path;
use infrajs\config\Config;
use infrajs\access\Access;

class Ydisk {
	public static $conf = array();
	public static function client() {
		return Once::func( function () {
			$diskClient = new DiskClient(Ydisk::$conf['key']);
			$diskClient->setServiceScheme(DiskClient::HTTPS_SCHEME);
			return $diskClient;
		});
	}
	/**
	* Заменяем папку на сервере папокой на Яндекс Диск
	*/
	public static function replace($sdir, $ydir) {
		Access::adminSetTime();
		$newdir = Path::mkdir('~ydisktmp/');
		$r = Ydisk::load('~ydisktmp/', $ydir); //Скачивает данные из Яндекс.Диска
		if (!$r) die('Не удалось скачать данные из Яндекс.Диска');
		
		$rsdir = Path::theme($sdir);
		if ($rsdir) {
			if (Path::theme('~ydisktmpold/')) Path::fullrmdir('~ydisktmpold/', true); //Удаляем старую папку
			$r = rename($rsdir, 'data/ydisktmpold/'); //Переименовываем старую папку
			if (!$r) die('Ydisk: не удалось переименовать старую папку');
		}

		$r = rename($newdir, Path::resolve($sdir)); //Переименовываем новую папка на место старой
		if (!$r) die('Ydisk: не удалось переименовать новую папку');
		if ($rsdir) {
			$r = Path::fullrmdir('~ydisktmpold/', true); //Удаляем старую папку
			if (!$r) die('Ydisk: не удалось удалить старую папку');
		}
	}
	public static function load($sdir, $ydir) {
		$gdir = Path::tofs(Path::mkdir($sdir));
		$diskClient = Ydisk::client();


		// Получаем имена файлов в каталоге
		$dirContent = $diskClient->directoryContents($ydir);
		unset($dirContent[0]);

		foreach ($dirContent as $dirItem) {
			$name = $dirItem['displayName'];
		    if ($dirItem['resourceType'] != 'dir') {
		    	$diskClient->downloadFile($dirItem['href'], $gdir, Path::tofs($name));
		    	
		    } else {

				$r = Ydisk::load($sdir.$name.'/', $ydir.$name.'/');
				if(!$r) return $r;
		    }
		}
		return true;
	}
}