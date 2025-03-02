<?php
$SECURITY->isLoggedIn();

$config = require __DIR__ . '/../includes/crystal/settings.config.php';


$lang = lang() ;
$action = action() ;

// Make sure the server is using UTC for consistent token generation.
date_default_timezone_set("UTC") ;

$apiKey = $config['api']['zoom']['jwt']['API_Key'];
$apiSecret = $config['api']['zoom']['jwt']['API_Secret'];

$zoom = new Zoom\ZoomAPI($apiKey, $apiSecret) ;
//echo '<pre>';
//$apiMeetings = $zoom->meetings->list('KrDrAl7pTC6ULwYVU64wSg', [
//            'page_size' => 8,
//            'page_number' => 1,
//            'type' => 'upcoming'
//        ]) ;
//
//if ($apiMeetings['code'] != 200 || !isset($apiMeetings['users']) ) {
//// TODO: Log error
////    var_dump($apiMeetings) ;
////    exit;
//}
//
//var_dump($apiMeetings) ;
//
//
//
//
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $p = (object) $_POST;

    $createArr = [
        "topic"=> $p->topic, //"string",
        "type"=> 2, //"integer",
        "start_time"=> $p->start_time, //"string [date-time]",
        "duration"=> $p->duration, //"integer",
        // "schedule_for"=> '', //"string",
        "timezone"=> $p->timezone, //"string",
        "password"=> $p->password, //"string",
        "agenda"=> $p->agenda, //"string",
        "settings"=> [
          "host_video"=> $p->option_video_host == "on"?"true":"false", //"boolean",
          "participant_video"=> $p->participant_video, //"boolean",
          "cn_meeting"=> $p->cn_meeting, //"boolean",
          "in_meeting"=> $p->in_meeting, //"boolean",
          "join_before_host"=> $p->join_before_host, //"boolean",
          "mute_upon_entry"=> $p->mute_upon_entry, //"boolean",
          "watermark"=> 0, //"boolean",
          "use_pmi"=> $p->use_pmi, //"boolean",
          "approval_type"=> $p->approval_type, //"integer",
        //   "registration_type"=> $p->password, //"integer",
          "audio"=> $p->audio, //"string",
          "auto_recording"=> $p->auto_recording, //"string",
        //   "enforce_login"=> $p->enforce_login, //"boolean",
          "allow_multiple_devices"=> $p->allow_multiple_devices, //"string",
          "alternative_hosts"=> $p->alternative_hosts, //"string",
          "global_dial_in_countries"=> [
            "string"
          ],
        //   "registrants_email_notification"=> "", //"boolean"
        ]
        ];
echo '<pre>';
var_dump($p);
var_dump($createArr);
echo '</pre>';
}
//
//if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
//
//
//}
?>

<html>


<head>
    <!-- <link rel="stylesheet" type="text/css" href="assets/content-tools.min.css"> -->
    <?php include __DIR__ . '/includes/header.inc.php'; ?>
    <link rel="stylesheet" href="../css/virtual.css">
    <link rel="stylesheet" href="assets/jodit/jodit.min.css">
    <script src="assets/jodit/jodit.js"></script>
</head>

<body class="">
    <div id="wrapper">
        <div id="main">
            <div class="inner">
                <?php include '../includes/title.inc.php'; ?>
                <section style="padding-block-end: 120px;">
                    <header class="main">
                        <h2>You are editing <small>(in) </small> <span
                                style="color:darkorange;"><?php echo ($lang == 'en') ? 'English' : 'Spanish' ?></span>
                        </h2>
                    </header>
                    <div class="row gtr-200">

                        <div class="col-12 col-12-medium">
                            <?php
                                if ($_SESSION['actionResponse'] != '') {
                                    echo "<div class='notification'>$_SESSION[actionResponse]</div>";
                                }

                                $_SESSION['actionResponse'] = '';
                                ?>
                        </div>
                    </div>
                    <?php if ($action != '' && ($action == 'edit' || $action == 'add') ) : ?>


                    <form method="post">
                        <div class="row">



                            <div class="col-8 col-12-xsmall">
                                <label for="topic">Topic</label>
                                <input type="text" id="topic" name="topic" maxlength="200" value="My Meeting"
                                    class="form-control" aria-invalid="false" required>
                            </div>


                            <div class="col-8 col-12-xsmall">
                                <label for="agenda">Description (Optional) </label>
                                <textarea class="sch-desc form-control" id="agenda" name="agenda" maxlength="2000"
                                    placeholder="Enter your meeting description" required></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <label for="start_date">When</label>
                                <input type="date" name="start_date" id="start_date" required class="date-inline">
                            </div>
                            <div class="col-3">
                                <label>&nbsp</label>
                                <select name="start_time" id="demo-category" required>
                                    <option aria-label="12:00" value="12:00">12:00</option>
                                    <option aria-label="12:30" value="12:30">12:30</option>
                                    <option aria-label="1:00" value="1:00">1:00</option>
                                    <option aria-label="1:30" value="1:30">1:30</option>
                                    <option aria-label="2:00" value="2:00">2:00</option>
                                    <option aria-label="2:30" value="2:30">2:30</option>
                                    <option aria-label="3:00" value="3:00">3:00</option>
                                    <option aria-label="3:30" value="3:30">3:30</option>
                                    <option aria-label="4:00" value="4:00">4:00</option>
                                    <option aria-label="4:30" value="4:30">4:30</option>
                                    <option aria-label="5:00" value="5:00">5:00</option>
                                    <option aria-label="5:30" value="5:30">5:30</option>
                                    <option aria-label="6:00" value="6:00">6:00</option>
                                    <option aria-label="6:30" value="6:30">6:30</option>
                                    <option aria-label="7:00" value="7:00">7:00</option>
                                    <option aria-label="7:30" value="7:30">7:30</option>
                                    <option aria-label="8:00" value="8:00">8:00</option>
                                    <option aria-label="8:30" value="8:30">8:30</option>
                                    <option aria-label="9:00" value="9:00">9:00</option>
                                    <option aria-label="9:30" value="9:30">9:30</option>
                                    <option aria-label="10:00" value="10:00">10:00</option>
                                    <option aria-label="10:30" value="10:30">10:30</option>
                                    <option aria-label="11:00" value="11:00">11:00</option>
                                    <option aria-label="11:30" value="11:30">11:30</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label>&nbsp</label>
                                <select name="start_time_2" id="start_time_2">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <label>Duration</label>
                                <select name="" id="" class="duration_hr" required>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label>&nbsp</label>
                                <select name="duration_min" id="duration_min">
                                    <option>0</option>
                                    <option>15</option>
                                    <option>30</option>
                                    <option>45</option>
                                </select>
                            </div>
                            <div class="col-4" style="display:block"></div>
                            <div class="col-8" style="">
                                <div id="error_duration" style="color:#FF1E5A; " class="hideme">
                                    Duration should be greater than 0 min
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <label>Time Zone (defaulted to Arizona)</label>
                                <select id="timezone" name="timezone" class="timezone">
                                    <option id="select-item-timezone-10" role="option" aria-label="(GMT-7:00) Arizona"
                                        tabindex="0" value="America/Phoenix"> (GMT-7:00) Arizona</option>
                                    <option id="select-item-timezone-0" role="option"
                                        aria-label="(GMT-11:00) Midway Island, Samoa" tabindex="0"
                                        class="zm-select-dropdown__item hover zm-select-dropdown__item--small"
                                        value="Pacific/Midway"> (GMT-11:00) Midway Island, Samoa
                                    </option>
                                    <option id="select-item-timezone-1" role="option" aria-label="(GMT-11:00) Pago Pago"
                                        tabindex="0" value="Pacific/Pago_Pago"> (GMT-11:00) Pago Pago
                                    </option>
                                    <option id="select-item-timezone-2" role="option" aria-label="(GMT-10:00) Hawaii"
                                        tabindex="0" value="Pacific/Honolulu"> (GMT-10:00) Hawaii
                                    </option>
                                    <option id="select-item-timezone-3" role="option" aria-label="(GMT-9:00) Alaska"
                                        tabindex="0" value="America/Anchorage"> (GMT-9:00) Alaska</option>
                                    <option id="select-item-timezone-4" role="option" aria-label="(GMT-9:00) Juneau"
                                        tabindex="0" value="America/Juneau"> (GMT-9:00) Juneau</option>
                                    <option id="select-item-timezone-5" role="option" aria-label="(GMT-8:00) Vancouver"
                                        tabindex="0" value="America/Vancouver"> (GMT-8:00) Vancouver
                                    </option>
                                    <option id="select-item-timezone-6" role="option"
                                        aria-label="(GMT-8:00) Pacific Time (US and Canada) " tabindex="0"
                                        class="zm-select-dropdown__item selected zm-select-dropdown__item--small"
                                        value="America/Los_Angeles"> (GMT-8:00) Pacific Time (US and
                                        Canada)
                                    </option>
                                    <option id="select-item-timezone-7" role="option" aria-label="(GMT-8:00) Tijuana"
                                        tabindex="0" value="America/Tijuana"> (GMT-8:00) Tijuana</option>
                                    <option id="select-item-timezone-8" role="option" aria-label="(GMT-7:00) Edmonton"
                                        tabindex="0" value="America/Edmonton"> (GMT-7:00) Edmonton</option>
                                    <option id="select-item-timezone-9" role="option"
                                        aria-label="(GMT-7:00) Mountain Time (US and Canada) " tabindex="0"
                                        value="America/Denver"> (GMT-7:00) Mountain Time (US and
                                        Canada)
                                    </option>
                                    <option id="select-item-timezone-10" role="option" aria-label="(GMT-7:00) Arizona"
                                        tabindex="0" value="America/Phoenix"> (GMT-7:00) Arizona</option>
                                    <option id="select-item-timezone-11" role="option" aria-label="(GMT-7:00) Mazatlan"
                                        tabindex="0" value="America/Mazatlan"> (GMT-7:00) Mazatlan</option>
                                    <option id="select-item-timezone-12" role="option" aria-label="(GMT-7:00) Chihuahua"
                                        tabindex="0" value="America/Chihuahua">
                                        (GMT-7:00) Chihuahua
                                    </option>
                                    <option id="select-item-timezone-13" role="option" aria-label="(GMT-6:00) Winnipeg"
                                        tabindex="0" value="America/Winnipeg"> (GMT-6:00) Winnipeg</option>
                                    <option id="select-item-timezone-14" role="option"
                                        aria-label="(GMT-6:00) Saskatchewan" tabindex="0" value="America/Regina">
                                        (GMT-6:00) Saskatchewan
                                    </option>
                                    <option id="select-item-timezone-15" role="option"
                                        aria-label="(GMT-6:00) Central Time (US and Canada) " tabindex="0"
                                        value="America/Chicago"> (GMT-6:00) Central Time (US and
                                        Canada)
                                    </option>
                                    <option id="select-item-timezone-16" role="option"
                                        aria-label="(GMT-6:00) Mexico City" tabindex="0" value="America/Mexico_City">
                                        (GMT-6:00) Mexico City</option>
                                    <option id="select-item-timezone-17" role="option" aria-label="(GMT-6:00) Guatemala"
                                        tabindex="0" value="America/Guatemala">
                                        (GMT-6:00) Guatemala
                                    </option>
                                    <option id="select-item-timezone-18" role="option"
                                        aria-label="(GMT-6:00) El Salvador" tabindex="0" value="America/El_Salvador">
                                        (GMT-6:00) El Salvador
                                    </option>
                                    <option id="select-item-timezone-19" role="option" aria-label="(GMT-6:00) Managua"
                                        tabindex="0" value="America/Managua"> (GMT-6:00) Managua</option>
                                    <option id="select-item-timezone-20" role="option"
                                        aria-label="(GMT-6:00) Costa Rica" tabindex="0" value="America/Costa_Rica">
                                        (GMT-6:00) Costa Rica</option>
                                    <option id="select-item-timezone-21" role="option"
                                        aria-label="(GMT-6:00) Tegucigalpa" tabindex="0" value="America/Tegucigalpa">
                                        (GMT-6:00) Tegucigalpa
                                    </option>
                                    <option id="select-item-timezone-22" role="option" aria-label="(GMT-6:00) Monterrey"
                                        tabindex="0" value="America/Monterrey">
                                        (GMT-6:00) Monterrey
                                    </option>
                                    <option id="select-item-timezone-23" role="option" aria-label="(GMT-5:00) Montreal"
                                        tabindex="0" value="America/Montreal"> (GMT-5:00) Montreal</option>
                                    <option id="select-item-timezone-24" role="option"
                                        aria-label="(GMT-5:00) Eastern Time (US and Canada) " tabindex="0"
                                        value="America/New_York"> (GMT-5:00) Eastern Time (US and
                                        Canada)
                                    </option>
                                    <option id="select-item-timezone-25" role="option"
                                        aria-label="(GMT-5:00) Indiana (East) " tabindex="0"
                                        value="America/Indianapolis"> (GMT-5:00) Indiana (East)
                                    </option>
                                    <option id="select-item-timezone-26" role="option" aria-label="(GMT-5:00) Panama"
                                        tabindex="0" value="America/Panama"> (GMT-5:00) Panama</option>
                                    <option id="select-item-timezone-27" role="option" aria-label="(GMT-5:00) Bogota"
                                        tabindex="0" value="America/Bogota"> (GMT-5:00) Bogota</option>
                                    <option id="select-item-timezone-28" role="option" aria-label="(GMT-5:00) Lima"
                                        tabindex="0" value="America/Lima"> (GMT-5:00) Lima</option>
                                    <option id="select-item-timezone-29" role="option" aria-label="(GMT-4:00) Halifax"
                                        tabindex="0" value="America/Halifax"> (GMT-4:00) Halifax</option>
                                    <option id="select-item-timezone-30" role="option"
                                        aria-label="(GMT-4:00) Puerto Rico" tabindex="0" value="America/Puerto_Rico">
                                        (GMT-4:00) Puerto Rico</option>
                                    <option id="select-item-timezone-31" role="option" aria-label="(GMT-4:00) Caracas"
                                        tabindex="0" value="America/Caracas"> (GMT-4:00) Caracas</option>
                                    <option id="select-item-timezone-32" role="option"
                                        aria-label="(GMT-4:00) Atlantic Time (Canada) " tabindex="0"
                                        value="Canada/Atlantic"> (GMT-4:00) Atlantic Time
                                        (Canada)
                                    </option>
                                    <option id="select-item-timezone-33" role="option" aria-label="(GMT-4:00) La Paz"
                                        tabindex="0" value="America/La_Paz"> (GMT-4:00) La Paz</option>
                                    <option id="select-item-timezone-34" role="option" aria-label="(GMT-4:00) Guyana"
                                        tabindex="0" value="America/Guyana"> (GMT-4:00) Guyana</option>
                                    <option id="select-item-timezone-35" role="option"
                                        aria-label="(GMT-3:30) Newfoundland and Labrador" tabindex="0"
                                        value="America/St_Johns"> (GMT-3:30) Newfoundland and
                                        Labrador
                                    </option>
                                    <option id="select-item-timezone-36" role="option" aria-label="(GMT-3:00) Santiago"
                                        tabindex="0" value="America/Santiago"> (GMT-3:00) Santiago</option>
                                    <option id="select-item-timezone-37" role="option"
                                        aria-label="(GMT-3:00) Montevideo" tabindex="0" value="America/Montevideo">
                                        (GMT-3:00) Montevideo
                                    </option>
                                    <option id="select-item-timezone-38" role="option" aria-label="(GMT-3:00) Recife"
                                        tabindex="0" value="America/Araguaina"> (GMT-3:00) Recife</option>
                                    <option id="select-item-timezone-39" role="option"
                                        aria-label="(GMT-3:00) Buenos Aires, Georgetown" tabindex="0"
                                        value="America/Argentina/Buenos_Aires"> (GMT-3:00) Buenos Aires,
                                        Georgetown
                                    </option>
                                    <option id="select-item-timezone-40" role="option" aria-label="(GMT-3:00) Greenland"
                                        tabindex="0" value="America/Godthab"> (GMT-3:00) Greenland
                                    </option>
                                    <option id="select-item-timezone-41" role="option" aria-label="(GMT-3:00) Sao Paulo"
                                        tabindex="0" value="America/Sao_Paulo"> (GMT-3:00) Sao Paulo</option>
                                    <option id="select-item-timezone-42" role="option" aria-label="(GMT-1:00) Azores"
                                        tabindex="0" value="Atlantic/Azores"> (GMT-1:00) Azores</option>
                                    <option id="select-item-timezone-43" role="option"
                                        aria-label="(GMT-1:00) Cape Verde Islands" tabindex="0"
                                        value="Atlantic/Cape_Verde"> (GMT-1:00) Cape Verde
                                        Islands
                                    </option>
                                    <option id="select-item-timezone-44" role="option"
                                        aria-label="(GMT+0:00) Universal Time UTC" tabindex="0" value="UTC"> (GMT+0:00)
                                        Universal Time UTC
                                    </option>
                                    <option id="select-item-timezone-45" role="option"
                                        aria-label="(GMT+0:00) Greenwich Mean Time" tabindex="0" value="Etc/Greenwich">
                                        (GMT+0:00) Greenwich Mean Time
                                    </option>
                                    <option id="select-item-timezone-46" role="option" aria-label="(GMT+0:00) Reykjavik"
                                        tabindex="0" value="Atlantic/Reykjavik">
                                        (GMT+0:00) Reykjavik
                                    </option>
                                    <option id="select-item-timezone-47" role="option" aria-label="(GMT+0:00) Dublin"
                                        tabindex="0" value="Europe/Dublin"> (GMT+0:00) Dublin</option>
                                    <option id="select-item-timezone-48" role="option" aria-label="(GMT+0:00) London"
                                        tabindex="0" value="Europe/London"> (GMT+0:00) London</option>
                                    <option id="select-item-timezone-49" role="option" aria-label="(GMT+0:00) Lisbon"
                                        tabindex="0" value="Europe/Lisbon"> (GMT+0:00) Lisbon</option>
                                    <option id="select-item-timezone-50" role="option"
                                        aria-label="(GMT+0:00) Nouakchott" tabindex="0" value="Africa/Nouakchott">
                                        (GMT+0:00) Nouakchott
                                    </option>
                                    <option id="select-item-timezone-51" role="option"
                                        aria-label="(GMT+1:00) Belgrade, Bratislava, Ljubljana" tabindex="0"
                                        value="Europe/Belgrade"> (GMT+1:00) Belgrade, Bratislava,
                                        Ljubljana
                                    </option>
                                    <option id="select-item-timezone-52" role="option"
                                        aria-label="(GMT+1:00) Sarajevo, Skopje, Zagreb" tabindex="0" value="CET">
                                        (GMT+1:00) Sarajevo, Skopje, Zagreb
                                    </option>
                                    <option id="select-item-timezone-53" role="option"
                                        aria-label="(GMT+1:00) Casablanca" tabindex="0" value="Africa/Casablanca">
                                        (GMT+1:00) Casablanca
                                    </option>
                                    <option id="select-item-timezone-54" role="option" aria-label="(GMT+1:00) Oslo"
                                        tabindex="0" value="Europe/Oslo"> (GMT+1:00) Oslo</option>
                                    <option id="select-item-timezone-55" role="option"
                                        aria-label="(GMT+1:00) Copenhagen" tabindex="0" value="Europe/Copenhagen">
                                        (GMT+1:00) Copenhagen
                                    </option>
                                    <option id="select-item-timezone-56" role="option" aria-label="(GMT+1:00) Brussels"
                                        tabindex="0" value="Europe/Brussels"> (GMT+1:00) Brussels</option>
                                    <option id="select-item-timezone-57" role="option"
                                        aria-label="(GMT+1:00) Amsterdam, Berlin, Rome, Stockholm, Vienna" tabindex="0"
                                        value="Europe/Berlin"> (GMT+1:00) Amsterdam, Berlin, Rome, Stockholm,
                                        Vienna
                                    </option>
                                    <option id="select-item-timezone-58" role="option" aria-label="(GMT+1:00) Amsterdam"
                                        tabindex="0" value="Europe/Amsterdam">
                                        (GMT+1:00) Amsterdam
                                    </option>
                                    <option id="select-item-timezone-59" role="option" aria-label="(GMT+1:00) Rome"
                                        tabindex="0" value="Europe/Rome"> (GMT+1:00) Rome</option>
                                    <option id="select-item-timezone-60" role="option" aria-label="(GMT+1:00) Stockholm"
                                        tabindex="0" value="Europe/Stockholm">
                                        (GMT+1:00) Stockholm
                                    </option>
                                    <option id="select-item-timezone-61" role="option" aria-label="(GMT+1:00) Vienna"
                                        tabindex="0" value="Europe/Vienna"> (GMT+1:00) Vienna</option>
                                    <option id="select-item-timezone-62" role="option"
                                        aria-label="(GMT+1:00) Luxembourg" tabindex="0" value="Europe/Luxembourg">
                                        (GMT+1:00) Luxembourg
                                    </option>
                                    <option id="select-item-timezone-63" role="option" aria-label="(GMT+1:00) Paris"
                                        tabindex="0" value="Europe/Paris"> (GMT+1:00) Paris</option>
                                    <option id="select-item-timezone-64" role="option" aria-label="(GMT+1:00) Zurich"
                                        tabindex="0" value="Europe/Zurich"> (GMT+1:00) Zurich</option>
                                    <option id="select-item-timezone-65" role="option" aria-label="(GMT+1:00) Madrid"
                                        tabindex="0" value="Europe/Madrid"> (GMT+1:00) Madrid</option>
                                    <option id="select-item-timezone-66" role="option"
                                        aria-label="(GMT+1:00) West Central Africa" tabindex="0" value="Africa/Bangui">
                                        (GMT+1:00) West Central Africa
                                    </option>
                                    <option id="select-item-timezone-67" role="option" aria-label="(GMT+1:00) Algiers"
                                        tabindex="0" value="Africa/Algiers"> (GMT+1:00) Algiers</option>
                                    <option id="select-item-timezone-68" role="option" aria-label="(GMT+1:00) Tunis"
                                        tabindex="0" value="Africa/Tunis"> (GMT+1:00) Tunis</option>
                                    <option id="select-item-timezone-69" role="option" aria-label="(GMT+1:00) Warsaw"
                                        tabindex="0" value="Europe/Warsaw"> (GMT+1:00) Warsaw</option>
                                    <option id="select-item-timezone-70" role="option"
                                        aria-label="(GMT+1:00) Prague Bratislava" tabindex="0" value="Europe/Prague">
                                        (GMT+1:00) Prague Bratislava
                                    </option>
                                    <option id="select-item-timezone-71" role="option" aria-label="(GMT+1:00) Budapest"
                                        tabindex="0" value="Europe/Budapest"> (GMT+1:00) Budapest</option>
                                    <option id="select-item-timezone-72" role="option" aria-label="(GMT+2:00) Helsinki"
                                        tabindex="0" value="Europe/Helsinki"> (GMT+2:00) Helsinki</option>
                                    <option id="select-item-timezone-73" role="option"
                                        aria-label="(GMT+2:00) Harare, Pretoria" tabindex="0" value="Africa/Harare">
                                        (GMT+2:00) Harare, Pretoria
                                    </option>
                                    <option id="select-item-timezone-74" role="option" aria-label="(GMT+2:00) Sofia"
                                        tabindex="0" value="Europe/Sofia"> (GMT+2:00) Sofia</option>
                                    <option id="select-item-timezone-75" role="option" aria-label="(GMT+2:00) Athens"
                                        tabindex="0" value="Europe/Athens"> (GMT+2:00) Athens</option>
                                    <option id="select-item-timezone-76" role="option" aria-label="(GMT+2:00) Bucharest"
                                        tabindex="0" value="Europe/Bucharest">
                                        (GMT+2:00) Bucharest
                                    </option>
                                    <option id="select-item-timezone-77" role="option" aria-label="(GMT+2:00) Nicosia"
                                        tabindex="0" value="Asia/Nicosia"> (GMT+2:00) Nicosia</option>
                                    <option id="select-item-timezone-78" role="option" aria-label="(GMT+2:00) Beirut"
                                        tabindex="0" value="Asia/Beirut"> (GMT+2:00) Beirut</option>
                                    <option id="select-item-timezone-79" role="option" aria-label="(GMT+2:00) Damascus"
                                        tabindex="0" value="Asia/Damascus"> (GMT+2:00) Damascus</option>
                                    <option id="select-item-timezone-80" role="option" aria-label="(GMT+2:00) Jerusalem"
                                        tabindex="0" value="Asia/Jerusalem"> (GMT+2:00) Jerusalem
                                    </option>
                                    <option id="select-item-timezone-81" role="option" aria-label="(GMT+2:00) Amman"
                                        tabindex="0" value="Asia/Amman"> (GMT+2:00) Amman</option>
                                    <option id="select-item-timezone-82" role="option" aria-label="(GMT+2:00) Tripoli"
                                        tabindex="0" value="Africa/Tripoli"> (GMT+2:00) Tripoli</option>
                                    <option id="select-item-timezone-83" role="option" aria-label="(GMT+2:00) Cairo"
                                        tabindex="0" value="Africa/Cairo"> (GMT+2:00) Cairo</option>
                                    <option id="select-item-timezone-84" role="option"
                                        aria-label="(GMT+2:00) Johannesburg" tabindex="0" value="Africa/Johannesburg">
                                        (GMT+2:00) Johannesburg
                                    </option>
                                    <option id="select-item-timezone-85" role="option" aria-label="(GMT+2:00) Khartoum"
                                        tabindex="0" value="Africa/Khartoum"> (GMT+2:00) Khartoum</option>
                                    <option id="select-item-timezone-86" role="option" aria-label="(GMT+2:00) Kiev"
                                        tabindex="0" value="Europe/Kiev"> (GMT+2:00) Kiev</option>
                                    <option id="select-item-timezone-87" role="option" aria-label="(GMT+3:00) Nairobi"
                                        tabindex="0" value="Africa/Nairobi"> (GMT+3:00) Nairobi</option>
                                    <option id="select-item-timezone-88" role="option" aria-label="(GMT+3:00) Istanbul"
                                        tabindex="0" value="Europe/Istanbul"> (GMT+3:00) Istanbul</option>
                                    <option id="select-item-timezone-89" role="option" aria-label="(GMT+3:00) Moscow"
                                        tabindex="0" value="Europe/Moscow"> (GMT+3:00) Moscow</option>
                                    <option id="select-item-timezone-90" role="option" aria-label="(GMT+3:00) Baghdad"
                                        tabindex="0" value="Asia/Baghdad"> (GMT+3:00) Baghdad</option>
                                    <option id="select-item-timezone-91" role="option" aria-label="(GMT+3:00) Kuwait"
                                        tabindex="0" value="Asia/Kuwait"> (GMT+3:00) Kuwait</option>
                                    <option id="select-item-timezone-92" role="option" aria-label="(GMT+3:00) Riyadh"
                                        tabindex="0" value="Asia/Riyadh"> (GMT+3:00) Riyadh</option>
                                    <option id="select-item-timezone-93" role="option" aria-label="(GMT+3:00) Bahrain"
                                        tabindex="0" value="Asia/Bahrain"> (GMT+3:00) Bahrain</option>
                                    <option id="select-item-timezone-94" role="option" aria-label="(GMT+3:00) Qatar"
                                        tabindex="0" value="Asia/Qatar"> (GMT+3:00) Qatar</option>
                                    <option id="select-item-timezone-95" role="option" aria-label="(GMT+3:00) Aden"
                                        tabindex="0" value="Asia/Aden"> (GMT+3:00) Aden</option>
                                    <option id="select-item-timezone-96" role="option" aria-label="(GMT+3:00) Djibouti"
                                        tabindex="0" value="Africa/Djibouti"> (GMT+3:00) Djibouti</option>
                                    <option id="select-item-timezone-97" role="option" aria-label="(GMT+3:00) Mogadishu"
                                        tabindex="0" value="Africa/Mogadishu">
                                        (GMT+3:00) Mogadishu
                                    </option>
                                    <option id="select-item-timezone-98" role="option" aria-label="(GMT+3:00) Minsk"
                                        tabindex="0" value="Europe/Minsk"> (GMT+3:00) Minsk</option>
                                    <option id="select-item-timezone-99" role="option" aria-label="(GMT+3:30) Tehran"
                                        tabindex="0" value="Asia/Tehran"> (GMT+3:30) Tehran</option>
                                    <option id="select-item-timezone-100" role="option" aria-label="(GMT+4:00) Dubai"
                                        tabindex="0" value="Asia/Dubai"> (GMT+4:00) Dubai</option>
                                    <option id="select-item-timezone-101" role="option" aria-label="(GMT+4:00) Muscat"
                                        tabindex="0" value="Asia/Muscat"> (GMT+4:00) Muscat</option>
                                    <option id="select-item-timezone-102" role="option"
                                        aria-label="(GMT+4:00) Baku, Tbilisi, Yerevan" tabindex="0" value="Asia/Baku">
                                        (GMT+4:00) Baku, Tbilisi, Yerevan
                                    </option>
                                    <option id="select-item-timezone-103" role="option" aria-label="(GMT+4:30) Kabul"
                                        tabindex="0" value="Asia/Kabul"> (GMT+4:30) Kabul</option>
                                    <option id="select-item-timezone-104" role="option"
                                        aria-label="(GMT+5:00) Yekaterinburg" tabindex="0" value="Asia/Yekaterinburg">
                                        (GMT+5:00) Yekaterinburg
                                    </option>
                                    <option id="select-item-timezone-105" role="option"
                                        aria-label="(GMT+5:00) Islamabad, Karachi, Tashkent" tabindex="0"
                                        value="Asia/Tashkent"> (GMT+5:00) Islamabad, Karachi,
                                        Tashkent
                                    </option>
                                    <option id="select-item-timezone-106" role="option" aria-label="(GMT+5:30) India"
                                        tabindex="0" value="Asia/Calcutta"> (GMT+5:30) India</option>
                                    <option id="select-item-timezone-107" role="option"
                                        aria-label="(GMT+5:30) Mumbai, Kolkata, New Delhi" tabindex="0"
                                        value="Asia/Kolkata"> (GMT+5:30) Mumbai, Kolkata, New
                                        Delhi
                                    </option>
                                    <option id="select-item-timezone-108" role="option"
                                        aria-label="(GMT+5:45) Kathmandu" tabindex="0" value="Asia/Kathmandu">
                                        (GMT+5:45) Kathmandu
                                    </option>
                                    <option id="select-item-timezone-109" role="option" aria-label="(GMT+6:00) Almaty"
                                        tabindex="0" value="Asia/Almaty"> (GMT+6:00) Almaty</option>
                                    <option id="select-item-timezone-110" role="option" aria-label="(GMT+6:00) Dacca"
                                        tabindex="0" value="Asia/Dacca"> (GMT+6:00) Dacca</option>
                                    <option id="select-item-timezone-111" role="option"
                                        aria-label="(GMT+6:00) Astana, Dhaka" tabindex="0" value="Asia/Dhaka">
                                        (GMT+6:00) Astana, Dhaka
                                    </option>
                                    <option id="select-item-timezone-112" role="option" aria-label="(GMT+6:30) Rangoon"
                                        tabindex="0" value="Asia/Rangoon"> (GMT+6:30) Rangoon</option>
                                    <option id="select-item-timezone-113" role="option"
                                        aria-label="(GMT+7:00) Novosibirsk" tabindex="0" value="Asia/Novosibirsk">
                                        (GMT+7:00) Novosibirsk
                                    </option>
                                    <option id="select-item-timezone-114" role="option"
                                        aria-label="(GMT+7:00) Krasnoyarsk" tabindex="0" value="Asia/Krasnoyarsk">
                                        (GMT+7:00) Krasnoyarsk
                                    </option>
                                    <option id="select-item-timezone-115" role="option" aria-label="(GMT+7:00) Bangkok"
                                        tabindex="0" value="Asia/Bangkok"> (GMT+7:00) Bangkok</option>
                                    <option id="select-item-timezone-116" role="option" aria-label="(GMT+7:00) Vietnam"
                                        tabindex="0" value="Asia/Saigon"> (GMT+7:00) Vietnam</option>
                                    <option id="select-item-timezone-117" role="option" aria-label="(GMT+7:00) Jakarta"
                                        tabindex="0" value="Asia/Jakarta"> (GMT+7:00) Jakarta</option>
                                    <option id="select-item-timezone-118" role="option"
                                        aria-label="(GMT+8:00) Irkutsk, Ulaanbaatar" tabindex="0" value="Asia/Irkutsk">
                                        (GMT+8:00) Irkutsk, Ulaanbaatar
                                    </option>
                                    <option id="select-item-timezone-119" role="option"
                                        aria-label="(GMT+8:00) Beijing, Shanghai" tabindex="0" value="Asia/Shanghai">
                                        (GMT+8:00) Beijing, Shanghai
                                    </option>
                                    <option id="select-item-timezone-120" role="option"
                                        aria-label="(GMT+8:00) Hong Kong SAR" tabindex="0" value="Asia/Hong_Kong">
                                        (GMT+8:00) Hong Kong SAR</option>
                                    <option id="select-item-timezone-121" role="option" aria-label="(GMT+8:00) Taipei"
                                        tabindex="0" value="Asia/Taipei"> (GMT+8:00) Taipei</option>
                                    <option id="select-item-timezone-122" role="option"
                                        aria-label="(GMT+8:00) Kuala Lumpur" tabindex="0" value="Asia/Kuala_Lumpur">
                                        (GMT+8:00) Kuala Lumpur
                                    </option>
                                    <option id="select-item-timezone-123" role="option"
                                        aria-label="(GMT+8:00) Singapore" tabindex="0" value="Asia/Singapore">
                                        (GMT+8:00) Singapore
                                    </option>
                                    <option id="select-item-timezone-124" role="option" aria-label="(GMT+8:00) Perth"
                                        tabindex="0" value="Australia/Perth"> (GMT+8:00) Perth</option>
                                    <option id="select-item-timezone-125" role="option" aria-label="(GMT+9:00) Yakutsk"
                                        tabindex="0" value="Asia/Yakutsk"> (GMT+9:00) Yakutsk</option>
                                    <option id="select-item-timezone-126" role="option" aria-label="(GMT+9:00) Seoul"
                                        tabindex="0" value="Asia/Seoul"> (GMT+9:00) Seoul</option>
                                    <option id="select-item-timezone-127" role="option"
                                        aria-label="(GMT+9:00) Osaka, Sapporo, Tokyo" tabindex="0" value="Asia/Tokyo">
                                        (GMT+9:00) Osaka, Sapporo, Tokyo
                                    </option>
                                    <option id="select-item-timezone-128" role="option" aria-label="(GMT+9:30) Darwin"
                                        tabindex="0" value="Australia/Darwin"> (GMT+9:30) Darwin</option>
                                    <option id="select-item-timezone-129" role="option"
                                        aria-label="(GMT+10:00) Vladivostok" tabindex="0" value="Asia/Vladivostok">
                                        (GMT+10:00) Vladivostok
                                    </option>
                                    <option id="select-item-timezone-130" role="option"
                                        aria-label="(GMT+10:00) Guam, Port Moresby" tabindex="0"
                                        value="Pacific/Port_Moresby"> (GMT+10:00) Guam, Port
                                        Moresby
                                    </option>
                                    <option id="select-item-timezone-131" role="option"
                                        aria-label="(GMT+10:00) Brisbane" tabindex="0" value="Australia/Brisbane">
                                        (GMT+10:00) Brisbane</option>
                                    <option id="select-item-timezone-132" role="option"
                                        aria-label="(GMT+10:30) Adelaide" tabindex="0" value="Australia/Adelaide">
                                        (GMT+10:30) Adelaide</option>
                                    <option id="select-item-timezone-133" role="option"
                                        aria-label="(GMT+11:00) Canberra, Melbourne, Sydney" tabindex="0"
                                        value="Australia/Sydney"> (GMT+11:00) Canberra, Melbourne,
                                        Sydney
                                    </option>
                                    <option id="select-item-timezone-134" role="option" aria-label="(GMT+11:00) Hobart"
                                        tabindex="0" value="Australia/Hobart"> (GMT+11:00) Hobart</option>
                                    <option id="select-item-timezone-135" role="option" aria-label="(GMT+11:00) Magadan"
                                        tabindex="0" value="Asia/Magadan"> (GMT+11:00) Magadan</option>
                                    <option id="select-item-timezone-136" role="option"
                                        aria-label="(GMT+11:00) Solomon Islands" tabindex="0" value="SST"> (GMT+11:00)
                                        Solomon Islands
                                    </option>
                                    <option id="select-item-timezone-137" role="option"
                                        aria-label="(GMT+11:00) New Caledonia" tabindex="0" value="Pacific/Noumea">
                                        (GMT+11:00) New Caledonia
                                    </option>
                                    <option id="select-item-timezone-138" role="option"
                                        aria-label="(GMT+12:00) Kamchatka" tabindex="0" value="Asia/Kamchatka">
                                        (GMT+12:00) Kamchatka
                                    </option>
                                    <option id="select-item-timezone-139" role="option"
                                        aria-label="(GMT+12:00) Fiji Islands, Marshall Islands" tabindex="0"
                                        value="Pacific/Fiji"> (GMT+12:00) Fiji Islands, Marshall
                                        Islands
                                    </option>
                                    <option id="select-item-timezone-140" role="option"
                                        aria-label="(GMT+13:00) Auckland, Wellington" tabindex="0"
                                        value="Pacific/Auckland"> (GMT+13:00) Auckland,
                                        Wellington
                                    </option>
                                    <option id="select-item-timezone-141" role="option"
                                        aria-label="(GMT+14:00) Independent State of Samoa" tabindex="0"
                                        value="Pacific/Apia"> (GMT+14:00) Independent State of
                                        Samoa
                                    </option>

                                </select>
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-4">
                                <label>Recurring meeting</label>
                                <input onclick="" id="option_rm" type="checkbox" name="option_rm">
                                <label for="option_rm">- Recurring meeting</label>
                            </div>
                        </div> -->
                        <!-- <div class="row">
                            <div class="col-4">
                                <label>Registration</label>
                                <input onclick="" id="option_registration" type="checkbox" name="option_registration">
                                <label for="option_registration">- Required</label>
                            </div>

            </div> -->


            <div class="row">
                <div class="col-4 col-12-small">
                    <input type="radio" id="option_schedulewithpmi_off" name="settings[option_schedulewithpmi]" value="off"
                        checked="">
                    <label for="option_schedulewithpmi_off">Generate Automatically</label>
                </div>
                <div class="col-4 col-12-small">
                    <input type="radio" id="option_schedulewithpmi_on" name="settings[option_schedulewithpmi]" value="on">
                    <label for="option_schedulewithpmi_on">Personal Meeting ID: 521 889 9669</label>
                </div>

            </div>
            <div class="row" style="margin-top: auto;">
                <div class="col-8 col-12-small">
                    <h3>Security</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-2 col-4-small">
                    <input onclick="" id="meeting_pass" type="checkbox">
                    <label for="meeting_pass">- Passcode</label>
                </div>
                <div class="col-4 col-8-small">
                    <input type="text" name="settings[meeting_pass]" id="meeting_pass">
                </div>
            </div>
            <div class="row">
                <div class="col-4 col-12-small">
                    <input onclick="" id="option_waiting_room" type="checkbox" name="settings[option_waiting_room]">
                    <label for="option_waiting_room">- Waiting Room</label>

                </div>
            </div>
            <div class="row">
                <div class="col-4 col-12-small">
                    <input onclick="" id="option_enforce_signed_in" type="checkbox" name="settings[option_enforce_signed_in]">
                    <label for="option_enforce_signed_in">- Require authentication to join</label>

                </div>
            </div>

            <div class="row" style="margin-top: auto;">
                <div class="col-12 col-12-small">
                    <h3>Video</h3>
                </div>
                <div class="col-2 col-12-small">
                    <label>Host</label>
                    <input type="radio" id="option_video_host_on" name="settings[option_video_host]" value="on" checked="">
                    <label for="option_video_host_on">on</label>
                </div>
                <div class="col-2 col-12-small">
                    <label>&nbsp</label>
                    <input type="radio" id="option_video_host_off" name="settings[option_video_host]" value="off">
                    <label for="option_video_host_off">off</label>
                </div>
            </div>
            <div class="row" style="margin-top: auto;">

                <div class="col-2 col-12-small">
                    <label>Participant</label>
                    <input type="radio" id="option_video_participant_on" name="settings[option_video_participants]" value="on"
                        checked=""><label for="option_video_participant_on">on</label>
                </div>
                <div class="col-2 col-12-small">
                    <label>&nbsp</label>
                    <input type="radio" id="option_video_participant_off" name="settings[option_video_participants]"
                        value="off"><label for="option_video_participant_off">off</label>
                </div>
            </div>
            <div class="row" style="margin-top: auto;">
                <div class="col-12 col-12-small">
                    <h3>Audio</h3>
                </div>
                <div class="col-2 col-12-small">
                    <input type="radio" id="option_audio_both" name="settings[option_audio]" value="both"><label
                        for="option_audio_both">Both</label></div>
                <div class="col-2 col-12-small"><input type="radio" id="option_audio_telephony" name="settings[option_audio]"
                        value="telephony" checked=""><label for="option_audio_telephony">Telephone</label></div>
                <div class="col-4 col-12-small"><input type="radio" id="option_audio_voip" name="settings[option_audio]"
                        value="voip"><label for="option_audio_voip">Computer
                        Audio</label></div>

            </div>
            <div class="row" style="margin-top: auto;">
                <div class="col-12 col-12-small">
                    <h3>Meeting Options</h3>
                </div>
                <div class="col-8 col-12-small">
                    <input onclick="" id="option_jbh" type="checkbox" name="settings[option_jbh]">
                    <label for="option_jbh">- Allow participants to join anytime</label>
                </div>
                <div class="col-8 col-12-small">
                    <input onclick="" id="option_mute_upon_entry" type="checkbox" name="settings[option_mute_upon_entry]">
                    <label for="option_mute_upon_entry">- Mute participants upon entry</label>

                </div>
                <div class="col-8 col-12-small">
                    <input onclick="" id="option_autorec" type="checkbox" name="settings[option_autorec]">
                    <label for="option_autorec">- Automatically record meeting</label>

                </div>
                <div class="col-8 col-12-small">
                    <input onclick="" id="option_join_meeting_region" type="checkbox" name="settings[option_join_meeting_region]">
                    <label for="option_join_meeting_region">- Approve or block entry for users from specific
                        countries/regions</label>

                </div>
            </div>
            <div class="row">
                <div class="col-8 col-12-small" style="text-align:right">
                    <input type="submit">
                </div>
            </div>
            <!-- <div class="col-8 col-12-xsmall">
                            <label for="topic">Alternative Hosts</label>
                            <input type="text" id="topic" name="select-alter" maxlength="200" value="My Meeting"
                                class="form-control" aria-invalid="false">
                        </div> -->

            </form>




            <?php else : ?>
            <div class="row gtr-200">
                <div class="col-12 col-12-medium">
                    Invalid action
                </div>
            </div>
            <?php endif ?>
            </section>
        </div>
    </div>
    <?php include 'includes/sidebar.inc.php'; ?>
    </div>
    </div>
    </div>
    <!-- The Modal -->
    <div id="filesModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content-basic">
            <span class="close-x" id="closeModal">&times;</span>
            <div class="box alt">
                <div class="row gtr-50 gtr-uniform" id='modalData'>

                </div>
            </div>
        </div>

    </div>
    <?php include __DIR__ . '/../includes/script.image.inc.php'; ?>
    <?php include __DIR__ . '/../includes/script.nav.inc.php'; ?>
    <script>
    function factory() {
        initModal('filesModal', 'openModal', 'closeModal', 'modalData', 'section-image', ['imageSelectHandler']);
        initModal('filesModal', 'openFileModal', 'closeModal', 'modalData', 'link_download_internal', [
            'docSelectHandler'
        ]);
    }

    window.onload = factory();
    </script>
    <script>
    var editor = new Jodit('#description_html', {
        filebrowser: {
            ajax: {
                url: 'assets/connector/index.php'
            },
            uploader: {
                url: 'assets/connector/index.php?action=fileUpload',
            }
        }
    });;
    </script>
</body>

</html>