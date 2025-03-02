<?php include_once 'includes' . DIRECTORY_SEPARATOR . 'meditation.inc.php'; ?>


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
        .stack-block {
            display: grid;
            grid-template: 1fr / 1fr;
            place-items: center;
        }

        .stack-block>* {
            grid-column: 1 / 1;
            grid-row: 1 / 1;
        }

        /* Make the “splash” circle responsive */
        .splash {
            /* Instead of width: 480px, do: */
            width: 100%;
            max-width: 700px;
            min-width: 400px;

            /* Keep it circular. aspect-ratio makes modern browsers
         maintain a square shape. (Fallback is explained below.) */
            aspect-ratio: 1 / 1;
            border-radius: 50%;

            /* Your existing styling */
            background-color: aliceblue;
            filter: drop-shadow(-1px 5px 10px #94a6ff);
            outline: 3px solid rgb(0, 164, 225);
            outline-offset: -5px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .svg-container {
            width: 100%;
            max-width: 700px;
            min-width: 400px;
            z-index: 2;
        }

        .svg-container svg {
            width: 100%;
            height: auto;
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

        /* If you want an image inside the .splash */
        .peace-block img {
            width: 80%;
            /* Make this scale within the splash */
            height: auto;
            max-width: 220px;
            /* Just an example cap */
        }

        .splash-container {
            min-height: 100vh;
            /* minimum height = screen height */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #peace_times_box * {
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

        path {
            fill: transparent;
        }

        text {
            font-size: 35px;
            fill: #FF9800;
        }
    </style>

    <div class="splash-container">
        <div class="center-container">
            <div class="title">Peace Meditation</div>
            <div class="stack-block">
                <div class="svg-container">
                    <svg viewBox="0 -15 600 600" preserveAspectRatio="xMidYMid meet">
                        <defs>
                            <!-- Top arc path: from left to right along the outer top of the circle -->
                            <path id="topArc" d="M 36,300 A 264,264 0 0,0 564,300" fill="none" />
                            <!-- Bottom arc path: from right to left along the outer bottom of the circle -->
                            <path id="bottomArc" d="M 564,300 A 264,264 0 0,1 36,300" fill="none" />
                        </defs>

                        <!-- Main circle -->
                        <circle cx="300" cy="300" r="240" stroke="#333" stroke-width="0" fill="none" />

                        <!-- Top text outside the circle -->
                        <text>
                            <textPath href="#topArc" startOffset="50%" text-anchor="middle">
                                FIGU-Interessengruppe für Missionswissen
                            </textPath>
                        </text>

                        <!-- Bottom text outside the circle, rotated to display correctly -->
                        <text transform="rotate(180,300,300)">
                            <textPath href="#bottomArc" startOffset="50%" text-anchor="middle">
                                Northern Arizona
                            </textPath>
                        </text>
                    </svg>
                </div>
                <section class="splash">
                    <div class="peace-block">
                        <img src="media/images/peace-symbol.jpg" alt="PEACE truly">
                    </div>

                </section>
            </div>
        </div>
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
        <div class="links-block">
            <a href="https://main.figucarolina.org/">FIGU Carolina Home</a>
            <span class="dot"></span>
            <a href="https://indexer.figucarolina.org/gnome/login">Indexer Login</a>
        </div>
        <div>
            Current Overpopulation
        </div>
   


    

    <?php

    if (strpos($_SERVER['REQUEST_URI'], "gnome") != true) {
        include_once 'includes' . DIRECTORY_SEPARATOR . 'script.overpopulation.inc.php';
    }

    ?>

</body>

</html>