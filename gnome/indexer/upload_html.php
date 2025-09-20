<?php

$SECURITY->isLoggedIn();

use gnome\classes\DBConnection;
use gnome\classes\MessageResource;

$lang = empty($_GET['lang']) ? 'en' : $_GET['lang'];

$backSlash = 'BKLSH';

$target_dir_base = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "media";
$target_dir_icons = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "media" . DIRECTORY_SEPARATOR . "icons" . DIRECTORY_SEPARATOR;
$target_dir = "";

$imageTypeArray = ['.gif', '.jpg', '.jpeg', '.png', '.svg'];
$fileTypeArray = ['.ods', '.xls', '.doc', '.docx', '.odt', '.pdf', '.rtf', '.txt', '.wpd'];
$videoTypeArray = ['.mkv', '.mov', '.mp4', '.mpg', '.mpeg', '.avi', '.ogv'];
$audioTypeArray = ['.mid', '.midi', '.mp3', '.mpa', '.ogg', '.wav'];
$compressionArray = ['.pkg', '.zip', '.rar'];

function getClass($file)
{

    $ext = '.' . pathinfo($file, PATHINFO_EXTENSION);

    global $imageTypeArray;
    global $fileTypeArray;
    global $videoTypeArray;
    global $audioTypeArray;
    global $compressionArray;

    if (in_array($ext, $imageTypeArray)) {
        return 'fileImg';
    } else if (in_array($ext, $fileTypeArray)) {
        return 'fileDoc';
    } else if (in_array($ext, $videoTypeArray)) {
        return 'fileVid';
    } else if (in_array($ext, $audioTypeArray)) {
        return 'fileAud';
    } else if (in_array($ext, $compressionArray)) {
        return 'fileComp';
    }

    return '';
}

function getIcon($file)
{

    $ext = '.' . pathinfo($file, PATHINFO_EXTENSION);

    global $imageTypeArray;
    global $fileTypeArray;
    global $videoTypeArray;
    global $audioTypeArray;
    global $compressionArray;

    if (in_array($ext, $imageTypeArray)) {
        return 'img';
    } else if (in_array($ext, $fileTypeArray)) {
        return 'other';
    } else if (in_array($ext, $videoTypeArray)) {
        return 'other';
    } else if (in_array($ext, $audioTypeArray)) {
        return 'other';
    } else if (in_array($ext, $compressionArray)) {
        return 'other';
    }

    return '';
}



function separatorAdd($str)
{
    return $str;
    global $backSlash;
    return str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, $str);
}

function separatorRemove($str)
{
    return $str;
    global $backSlash;
    return str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $str);
}


if (isset($_GET['folder']) && $_GET['folder'] != '') {
    $target_dir = $target_dir_base . separatorRemove($_GET['folder']);
} else {
    $target_dir = $target_dir_base;
}

// var_dump($_GET['folder']);


if (isset($_GET['back'])) {
    $sString = str_replace(DIRECTORY_SEPARATOR . "..", "", separatorRemove($_GET['folder']));
    $twig = substr($sString, 0, strrpos($sString, DIRECTORY_SEPARATOR));
    $target_dir = $target_dir_base . $twig;
}


$returnObject = [
    'directory' => [],
    'images' => []
];



function isEmptyDir($dir)
{
    return (($files = @scandir($dir)) && count($files) <= 2);
}

$q = count(glob("$target_dir/*")) == 0;


// check and set dir it exists

// var_dump(isEmptyDir($target_dir));


if ($opendir = opendir($target_dir)) {
    while (($file = readdir($opendir)) !== false) {

        $dName = $target_dir . DIRECTORY_SEPARATOR . $file;

        if ($file != "." && $file != ".." && $file != "icons") {
            if (is_dir($dName)) {
                $returnObject['directory'][] = $file;
            } else {
                $returnObject['images'][] = $file;
            }
        }
    }
    closedir($opendir);
}


$htmlBlock = "<div class='row'>";

$currDir = str_replace($target_dir_base, '', $target_dir);

$target_dir_clean = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $target_dir);
//  $target_dir_clean = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, $target_dir_clean);
$htmlBlock .= "<div class='col-1'>
        <div class='select'>
            <figure>
                <img src='$target_dir_base/icons/blue-folder-add.jpg' class='addDirectory img-fluid' data-dir=\"$target_dir_clean\" >
                <figcaption class='fit'>Add Folder</figcaption>
            </figure>
        </div>
    </div>";
//}

if ($target_dir_base != $target_dir) {
    // var_dump($currDir);
    $dirThis = separatorAdd($currDir . DIRECTORY_SEPARATOR . "..");
    $dirThisString = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dirThis);
    // var_dump($dirThis);
    $htmlBlock .= "<div class='col-1'>
        <div class='select'>
            <figure>
                <img src='$target_dir_base/icons/blue-folder-back.jpg' class='goBack img-fluid' data-dir=\"$dirThisString\" >
                <figcaption class='fit'>Back</figcaption>
            </figure>
        </div>
    </div>";
}



foreach ($returnObject['directory'] as $dir) {

    $newDir = separatorAdd($currDir . DIRECTORY_SEPARATOR . $dir);
    $htmlBlock .= "<div class='col-1'>
        <div class='select'>
            <figure>
                <img src='$target_dir_base/icons/blue-folder.jpg' class='folderDir img-fluid' data-dir=\"$newDir\" >
                <figcaption class='fit'>$dir</figcaption>
            </figure>
        </div>
    </div>";
}

$htmlBlock .= "</div>";


foreach ($returnObject['images'] as $file) {
    $class = getClass($file);
    $icon = getIcon($file);

    if ($icon == 'img') {
        $imgPath = $target_dir . DIRECTORY_SEPARATOR . $file;
    } else {
        $imgPath = $target_dir_icons . 'other.jpg';
    }

    $imgPathArg = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, $target_dir . DIRECTORY_SEPARATOR . $file);
    $nameSpace = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $currDir . DIRECTORY_SEPARATOR . $file);
    $htmlBlock .= "<div class='row mb-2 img-list' >
        <div class='col-2'>
            <button type='button' class='btn btn-danger move-to-trash' data-delete='$imgPathArg'><i class='bi bi-trash'></i></button>
        </div>
        <div class='col-9'>
            $file
        </div> 
        <div class='col-1'>               
            <img src='$imgPath' data-file='$imgPathArg' data-dir='$nameSpace' class='$class img-fluid' >
        </div>
    </div>";
}

echo $htmlBlock;
