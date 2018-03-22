<?php

//    files in directory
    $files = array();
//    only methods type 1
    $type1 = "/getMock\(\'([a-zA-Z\\]+)\',([a-zZA-Z\s\(\)\']+)\;/";
//    methods empty others type 2
    $type2 = "";
//    methods not empty other type 3
    $type3 = "/->getMock\(\'([^,]*,){4}[\w\s]*\)\);/";;


//$regexStep1 = "/getMock\((\'[\w\\]+\'),([\w\s\(\)\']+)/";
//$regexStep2 = "/(?<=getMockBuilder\(\'[a-zA-Z\\]+\')(.*)(?=\)\;)/";


    $files = getListFiles();
    sendToCorrectType($files,$type1);

    function sendToCorrectType($files,$type1) {
        foreach ($files as $dir) {
            var_dump($dir);
            if (file_exists ($dir) ) {
                $str = file_get_contents($dir);
                $str = preg_replace_callback($type1,evaluateType1, $str);
                file_put_contents($dir, $str);
            }
        }
    }

    function evaluateType1($Match) {
        var_dump("damierrrrrrrrrr");
        var_dump($Match[0]);
        return "+++++++";
    }

    function getListFiles() {
    //    Directory to find "getmock"
        $rii_ = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./EMRDelegator/tests/EMRAdminTest/unit/tests'));
    //    files in directory
        $files_ = array();
        foreach ($rii_ as $file) {

            if ($file->isDir()){
                continue;
            }
            $files_[] = $file->getPathname();
        }
        return $files_;
    }

//getMock\(\'([a-zA-Z\\]+)\',([a-zZA-Z\s\(\)\']+)\;