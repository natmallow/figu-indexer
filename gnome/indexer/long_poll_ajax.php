<?php
header("Content-Type: application/json");

$logFile = '/home/figuadqo/public_html/gnome/classes/service/cron/logfile.txt';

if ($_SERVER['HTTP_ENVIRONMENT'] == 'dev')
    $logFile = '/xampp/htdocs/figuarizona/gnome/classes/service/cron/logfile.txt';


$lastModificationTime = isset($_GET['timestamp']) ? $_GET['timestamp'] : 0;

if (!file_exists($logFile)) {
    echo json_encode(["error" => "Log file does not exist."]);
    http_response_code(404); // Set HTTP status code to 404
    exit;
}

$currentModificationTime = filemtime($logFile);

// Hold the request until there's a change or a timeout (30 seconds)
$timeout = 30; // 30 seconds
$start = time();

while (time() - $start < $timeout) {
    clearstatcache();
    $currentModificationTime = filemtime($logFile);
    
    if ($currentModificationTime > $lastModificationTime) {
        $logContent = file_get_contents($logFile);
        echo json_encode([
            'timestamp' => $currentModificationTime,
            'log' => $logContent
        ]);
        exit;
    }
    
    usleep(500000); // sleep for 500ms to reduce CPU usage
}

// If no update, return current timestamp
echo json_encode([
    'timestamp' => $currentModificationTime,
    'log' => ''
]);
?>