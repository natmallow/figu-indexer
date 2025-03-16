<?php

$SECURITY->isLoggedIn();

$lang = empty($_GET['lang' ]) ? 'en' : $_GET['lang'];

$target_dir = ".." . DIRECTORY_SEPARATOR .".." . DIRECTORY_SEPARATOR . "media" . DIRECTORY_SEPARATOR;


$currentUploads = [];


function readAllFiles($root = null)
{
    global $target_dir;
    $root = is_null($root) ? $target_dir : $root;

    $files  = ['files' => [], 'dirs' => []];
    $directories  = [];
    $last_letter  = $root[strlen($root) - 1];
    $root  = ($last_letter == '\\' || $last_letter == '/') ? $root : $root . DIRECTORY_SEPARATOR;

    $directories[]  = $root;

    while (sizeof($directories)) {
        $dir  = array_pop($directories);
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $file  = $dir . $file;
                if (is_dir($file)) {
                    $directory_path = $file . DIRECTORY_SEPARATOR;
                    array_push($directories, $directory_path);
                    $files['dirs'][]  = $directory_path;
                } elseif (is_file($file)) {
                    $files['files'][]  = $file;
                }
            }
            closedir($handle);
        }
    }

    return $files;
}


// check and set dir it exists
// if ($opendir = opendir($target_dir)) {

//     // read dir
//     while (($file = readdir($opendir)) !== false) {
//         if ($file != "." && $file != "..")
//             $images[] = $file;
//     }
// }

return readAllFiles();
