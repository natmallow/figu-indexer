<?php

$SECURITY->isLoggedIn();

$lang = empty($_GET['lang' ]) ? 'en' : $_GET['lang'];

$fileOkArray = ['.gif', '.jpg', '.jpeg', '.png', '.svg', 
'.ods', '.xls', '.doc', '.docx', '.odt', '.pdf', '.rtf', 
'.txt', '.wpd', '.mkv', '.mov', '.mp4', '.mpg', '.mpeg', 
'.avi', '.ogv', '.mid', '.midi', '.mp3', '.mpa', '.ogg', 
'.wav', '.pkg', '.zip', '.rar'];

$targetDir = "../media/";

$actionResponse = '';

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST' && $_GET['action'] == 'newDir'){

   $nPath = $_POST['dir'].DIRECTORY_SEPARATOR.$_POST['name'];
   mkdir( $nPath , 0755, true);
   echo json_encode(['response' => 'Folder Created', 'dir'=> $_POST['dir'], 'name' => $_POST['name'] ]);
    exit();
} elseif ($method == 'DELETE') {
    // Method is DELETE

    $filePath = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $_GET['file']);

    unlink($filePath);

    echo json_encode(['response' => 'File has been removed', 'file_name' => $filePath ]);
    exit();
}




// if ($_POST['action'] == 'upload') {

$targetFile = $targetDir . $_POST["directory"] . basename(str_replace(' ', '-', strtolower($_FILES["fileToUpload"]["name"])));
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if (isset($_POST["submitted"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

    // if ($check !== false) {
    //     $actionResponse .= "File is an image - " . $check["mime"] . ".<br>";
    //     $uploadOk = 1;
    // } else {
    //     $actionResponse .= "File is not an image.<br>";
    //     $uploadOk = 0;
    // }

    if (!isset($_POST["directory"])) {
        $actionResponse .= "Please Select a Directory<br>";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($targetFile)) {
    $actionResponse .= "Sorry, file already exists.<br>";
    $uploadOk = 0;
}
// Check file size 1 mb
if ($_FILES["fileToUpload"]["size"] > 1000000) {
    $actionResponse .= "Sorry, your file is too large.<br>";
    $uploadOk = 0;
}

$allowedFormats = [];

// Allow certain file formats
if (in_array($imageFileType, $fileOkArray))
 {
    $actionResponse .= "Sorry, '$imageFileType' is invalid.<br>";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $actionResponse .= "Sorry, your file was not uploaded.<br>";
    // if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
        $actionResponse .= "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
    } else {
        $actionResponse .= "Sorry, there was an error uploading your file.";
    }
}

$_SESSION['actionResponse'] = $actionResponse;

header("Location: ./uploads.php");
exit();
