<?php
namespace akiyatkin\ydisk;
use infrajs\ans\Ans;
use Yandex\Disk\DiskClient;
use infrajs\once\Once;
use infrajs\path\Path;
use infrajs\config\Config;
use infrajs\access\Access;
use akiyatkin\fs\FS;

class Ydisk {
	public static $conf = array();
	public static function client() {
		return Once::func( function () {
			$diskClient = new DiskClient(Ydisk::$conf['key']);
			$diskClient->setServiceScheme(DiskClient::HTTPS_SCHEME);
			return $diskClient;
		});
	}
	public static function replaceAll() {
		$conf = Ydisk::$conf;
		foreach ($conf['sync'] as $rule) {
			Ydisk::replace($rule['site'],$rule['ysrc']);	
		}
	}
	/**
	* Заменяем папку на сервере папокой на Яндекс Диск
	*/
	public static function replace($sdir, $ydir) {
		Access::adminSetTime();
		$newdir = Path::mkdir('~ydisktmp/');
		$r = Ydisk::load('~ydisktmp/', $ydir, $sdir); //Скачивает данные из Яндекс.Диска

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
	public static function load($sdir, $ydir, $sdirorig) {
		
		$gdir = Path::mkdir($sdir);
		$diskClient = Ydisk::client();
		// Получаем имена файлов в каталоге
		$dirContent = $diskClient->directoryContents($ydir);
		unset($dirContent[0]);
		foreach ($dirContent as $dirItem) {
			$name = $dirItem['displayName'];
		    if ($dirItem['resourceType'] != 'dir') {
		    	//echo '<pre>';
		    	//
		    	$load = false;
		    	if (!FS::is_file($gdir.$name)) {
			    	if (!FS::is_file($sdirorig.$name)) $load = true;
			    	else {
			    		$stime = FS::filemtime($sdirorig.$name);
			    		$ytime = strtotime($dirItem['lastModified']);	
			    		//echo $sdirorig.$name.'<br>';
			    		//echo 'Время сервера'.$stime.' '.date('j.m.Y H:i',$stime).'<br>';
			    		//echo 'Время Яндекса'.$ytime.' '.date('j.m.Y H:i',$ytime).'<br>';
			    		$load = $ytime > $stime;
			    	}
			    }

				//var_dump($load);
		    	//echo $time.'<br>';
		    	//print_r($dirItem);
		    	if ($load) {
		    		$diskClient->downloadFile($dirItem['href'], $gdir, Path::tofs($name));
		    	} else{
		    		FS::rename($sdirorig.$name, $gdir.$name);
		    	}
		    	
		    	
		    	
				
		    } else {
		    	usleep(300000);//1000000 - 1 сек - 6 нулей
				$r = Ydisk::load($sdir.$name.'/', $ydir.$name.'/', $sdirorig.$name.'/');
				if (!$r) return $r;
		    }
		}
		return true;
	}
}