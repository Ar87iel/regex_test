<?php

	$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./EMRDelegator/tests/EMRDelegatorTest/unit/tests/Service'));
	$patron01 = "/getMock(?=\(\'([^\,]+)\'\))/";
	$files = array(); 

	foreach ($rii as $file) {

	    if ($file->isDir()){
	        continue;
	    }
	    $files[] = $file->getPathname(); 
	}

	foreach ($files as $dir) {
		var_dump($dir);
		if (file_exists ($dir) ) {
			var_dump("damierrrrrrrrrrr");


			$str = file_get_contents($dir);
			
			$str = preg_replace($patron01, 'createMock', $str);

			file_put_contents($dir, $str);
		}
	}
?>