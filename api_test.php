<?php

// require dirname(__DIR__) . './vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';
$config = require_once __DIR__ . '/includes/crystal/settings.config.php'; // Defines API_KEY and API_SECRET constants
// Make sure the server is using UTC for consistent token generation.
date_default_timezone_set("UTC");

$apiKey = $config['api']['zoom']['jwt']['API_Key'];
$apiSecret = $config['api']['zoom']['jwt']['API_Secret'];

$zoom = new Zoom\ZoomAPI($apiKey, $apiSecret);
echo '<pre>';
$users_resp = $zoom->meetings->list('KrDrAl7pTC6ULwYVU64wSg', [
            'page_size' => 8,
            'page_number' => 1,
            'type' => 'upcoming'
        ]);

if ($users_resp['code'] != 200 || !isset($users_resp['users'])) {
// TODO: Log error
//    var_dump($users_resp);
//    exit;
}

// var_dump($users_resp);

echo '<table>';
    echo '<tr><th>uuid';
    echo '<td>id';
    echo '<td>host_id';
    echo '<td>topic';
    echo '<td>type';
    echo '<td>start_time';
    echo '<td>duration';
    echo '<td>timezone';
    echo '<td>created_at';
    echo '<td>join_url';
foreach ($users_resp['meetings'] as $key => $value) {
    echo '<tr><td>'.$value['uuid'];
    echo '<td>'.$value['id'];
    echo '<td>'.$value['host_id'];
    echo '<td>'.$value['topic'];
    echo '<td>'.$value['type'];
    echo '<td>'.$value['start_time'];
    echo '<td>'.$value['duration'];
    echo '<td>'.$value['timezone'];
    echo '<td>'.$value['created_at'];
    echo '<td>'.$value['join_url'];
            
}
echo '</table>';
?>
<br><br><br>
------ create_new meeting ------
<br>
<?php

$meeting_resp = $zoom->meetings->create('KrDrAl7pTC6ULwYVU64wSg', [
            'topic' => 'Nates testing',
            'type' => 2, // means scheduled meeting
            'start_time' => '2021-12-25T16:54:14', // yyyy-MM-ddT12:02:00
            'duration' => '120', // in minutes
            'timezone' => 'America/Phoenix',
            'password' => 'Gnarly', // Passcode to join the meeting. By default, passcode may only contain the following characters: [a-z A-Z 0-9 @ - _ *] and can have a maximum of 10 characters.
        ]);

var_dump($meeting_resp);
