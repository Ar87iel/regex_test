<?php
	// $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./deskmodule/tests/unit'));
	$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./EMRDelegator/tests/EMRAdminTest/unit'));
	$patron01 = "/getMock(?=\(\'([^\,]+)\'\))/";
	// $patron02 = "/getMock(?=\(([^\,]+)\))/";
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
			$str = file_get_contents($dir);
			$str = preg_replace($patron01, 'createMock', $str);
            // $str = preg_replace($patron02, 'createMock', $str);
			file_put_contents($dir, $str);
		}
	}
?>