<?php include_once 'includes'.DIRECTORY_SEPARATOR.'meditation.inc.php'; ?>


<!DOCTYPE HTML>


<head>

    <base href="/" />
    <title>FIGU-Interessengruppe f&uuml;r Missionswissen Northern Arizona</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="css/_custom.css">
    <link rel="stylesheet" type="text/css" href="css/_main.css">
    <link rel="shortcut icon" href="/favicon_az.ico" />
    <script src="/js/lib/jquery/jquery.min.js"></script>
    <script src="/js/lib/browser/browser.min.js"></script>
    <script src="/js/lib/breakpoints/breakpoints.min.js"></script>
    <script src="/js/util.js"></script>
</head>
<html>

<body>
    <style>
        .splash {
            height: 282px;
            width: 611px;
            border-radius: 36px;
            display: flex;
            flex-direction: row;
            background-color: aliceblue;
            gap: 11px;
            padding: 26px;
            filter: drop-shadow(-1px 5px 10px #94a6ff);
            outline: 3px solid rgb(0 164 225);
            outline-offset: -5px;
        }

        .title {
            color: #2cb996;
            font-size: 56px;
            text-align: center;
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
        }

        .peace-block {}

        .peace-block img {
            max-width: 222px;
        }

        .splash-container {
            min-height: 100vh;
            /* minimum height = screen height */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #peace_times_box *  {
            font-size: 14.5px !important;
        }

        .links-block {
            font-size: 23px;
            text-align: center;
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
            gap: 16px;
            align-items: center;
        }

        .dot {
            width: 19px;
            height: 19px;
            background-color: #f79789;
            border-radius: 100%;
            display: inline-flex;
            line-height: 45px;
        }

        .center-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: center;
        }
    </style>

    <div class="splash-container">
        <div class="center-container">
            <div class="title">Peace Meditation</div>
            <section class="splash">
                <div class="peace-block">
                    <img src="media/images/peace-symbol.jpg" alt="PEACE truly">
                </div>
                <div class="clock">
                    <div id="peace_times_box">
                        <p>
                            <strong><?php print 'Saturday, ' . $nMonth . ' ' . $date1 ?></strong> <br>
                            <span>First Meditation @ 5:30 PM UTC<br />
                                (<span class="meditation" id='medTime1'>Meditation</span>) 
                            </span><br />
                            <span>Second Meditation @ 7:00 PM UTC <br />
                                (<span class="meditation" id='medTime2'>Meditation</span>) 
                            </span>
                        </p>
                        <p>
                            <strong><?php print 'Sunday, ' . $nMonth . ' ' . $date2  ?> </strong> <br>
                            <span>First Meditation @ 7:00 PM UTC <br>(<span class="meditation" id='medTime3'>Meditation</span>)</span>
                        </p>
                    </div>

                    <script type="text/javascript">
                        //meditation start
                        med_1 = new do_cd;
                        med_1.m1();

                        med_2 = new do_cd;
                        med_2.m2();

                        med_3 = new do_cd;
                        med_3.m3();
                    </script>
                </div>
            </section>
            <div class="links-block">
                <a href="https://main.figucarolina.org/">FIGU Carolina Home</a>
                <span class="dot"></span>
                <a href="https://indexer.figucarolina.org/gnome/login">Indexer Login</a>
            </div>
            <div>
             Current Overpopulation
            </div>
        </div>

        
    </div>

    <?php 

        if (strpos($_SERVER['REQUEST_URI'], "gnome") != true) {
            include_once 'includes'.DIRECTORY_SEPARATOR.'script.overpopulation.inc.php';
        }

     ?> 

</body>

</html>