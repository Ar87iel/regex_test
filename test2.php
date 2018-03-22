<?php

//    only methods type 1
    $type1 = "";
//    methods empty others type 2
    $type2 = "";
//    methods not empty other type 3
    $type3 = "";


	$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./EMRDelegator/tests/EMRDelegatorTest/unit/tests/Service'));
	$regexStep1 = "/getMock(?=\(\'(.*\,.*)\'\)\))/";
	$regexStep2 = "/(?<=getMockBuilder\(\'[a-zA-Z\\]+\')(.*)(?=\)\;)/";
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
			
			$str = preg_replace($regexStep1, 'getMockBuilder', $str);

			file_put_contents($dir, $str);
		}
	}


	foreach ($files as $dir) {
		var_dump($dir);
		if (file_exists ($dir) ) {

			$str2 = file_get_contents($dir);

			$str2 = preg_replace($regexStep2, 'getMockBuilder', $str2);

			file_put_contents($dir, $str2);
		}
	}


	// preg_match(pattern, subject)

?>