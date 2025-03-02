<?php
$SECURITY->isLoggedIn();

$config = require __DIR__ . '/../includes/crystal/settings.config.php';

$lang = lang();
$action = action();

$nameClient = "Zoom";

// Make sure the server is using UTC for consistent token generation.
date_default_timezone_set("UTC");

$apiKey = $config['api']['zoom']['jwt']['API_Key'];
$apiSecret = $config['api']['zoom']['jwt']['API_Secret'];



$zoom = new Zoom\ZoomAPI($apiKey, $apiSecret);

$apiMeetings = $zoom->meetings->list('KrDrAl7pTC6ULwYVU64wSg', [
            'page_size' => 8,
            'page_number' => 1,
            'type' => 'upcoming'
        ]);

if ($apiMeetings['code'] != 200 || !isset($apiMeetings['users'])) {
// TODO: Log error
//    var_dump($apiMeetings);
//    exit;
}


/**
  API return Reference 
  @var int page_count 1
  @var int page_number 1
  @var int page_size 8
  @var int total_records 1
  @var arr meetings 
  =>  
     @var uuid string(24)  "YotGXG/iQsiJ0GI0uNssAw=="
      @var id int 82693244629
      @var host_id string "KrDrAl7pTC6ULwYVU64wSg"
      @var topic string "Nates testing"  
      @var type int(2) 2 = future 
      @var start_time string(20) "2021-12-25T23:54:14Z"
      @var duration int(120) in minutes
      @var timezone string(15) "America/Phoenix"
      @var created_at string(20) "2020-12-28T20:22:09Z"
      @var join_url string(74) "https://us02web.zoom.us/j/82693244629?pwd=bk9vOGhBb2NEbUlnV3MyV2xtUmJWQT09"
 */

//$respObj = var_dump($apiMeetings);

?>

<html>

    <head>
        <?php include __DIR__ . '/includes/header.inc.php'; ?>
    </head>

    <body class="">
        <div id="wrapper">
            <div id="main">
                <div class="inner">
                    <?php include '../includes/title.inc.php'; ?>
                    <section>
                        <header class="main">
                            <h2 style="display: inline-block;">You are editing <?=$nameClient?>-Meetings <small>(in)</small> <span style="color:darkorange;"><?php echo ($lang == 'en') ? 'English' : 'Spanish' ?></span></h2>
                        </header>

                        <div class="row gtr-200">
                            <div class="col-4 col-5-medium">
                                Events - <a href="/gnome/virtual.php?action=add&lang=<?= $lang ?>" class="button small" >Create New <?=$nameClient?> Meeting</a>
                                
                            </div>
                            <div class="col-8 col-7-medium align-center">
                                <div class='' id="notification">
                                    <?php
                                    if ($_SESSION['actionResponse'] != '') {
                                        echo "<div class='notification fadeOut'>$_SESSION[actionResponse]</div>";
                                    }
                                    $_SESSION['actionResponse'] = '';
                                    ?>
                                </div>
                            </div>
                            <div class="col-12"><hr></div>
                        </div>

                        <?php
                        $title = '';
                        $cTitle = '';
                        $mCount = count($apiMeetings['meetings']);
                        for ($i = 0; $i < $mCount; $i++) :
                                $meeting = $apiMeetings['meetings'][$i]
                        ?>
                            <div class="row alt-rows" style="">
                                <div class="col-5"  >
                                    <?= $meeting["topic"] ?>
                                </div>
                                <div class="col-3"  >
                                    
                                    <?= timeformat($meeting["start_time"]) ?> 
                                    
                                </div>                           
                                <div class="col-4" style="text-align: right; padding-right: 5px;" >
                                    <a href="/gnome/event.php?id=<?= $meeting["event_id"] ?>&action=edit&lang=<?= $lang ?>" class="button primary small" style="margin: 3px;">Start</a>  
                                    <a href="/gnome/event.php?id=<?= $meeting["event_id"] ?>&action=edit&lang=<?= $lang ?>" class="button primary small">Edit</a> 
                                    <a href="<?= $meeting["join_url"] ?>" target="_blank" class="button primary small">Delete</a> 
                                </div>
                            </div>
                        <?php
                          endfor;
                        ?>
                        <?php if(!$mCount): ?>
                            <div class="row">
                                <div class="col-12" style="text-align: center"> There are no Zoom Events </div>
                            </div>
                        <?php endif; ?>
                </section>
            </div>
        </div>
        <?php include 'includes/sidebar.inc.php'; ?>
    </div>
    <?php include __DIR__ . '/includes/script.inc.php'; ?>
    <?php include __DIR__ . '/../includes/script.nav.inc.php'; ?>
</body>

</html>