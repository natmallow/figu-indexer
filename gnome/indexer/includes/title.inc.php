<?php

$index = str_contains($_SERVER["REQUEST_URI"], 'gnome')? "/gnome/index.php": "/index.php";

?>
<header id="header">
    <div>
        <h2><a href=<?=$index?> ><strong>FIGU</strong>-Interessengruppe f&uuml;r Missionswissen Northern Arizona</a> - Admin</h2>
    </div>
    <!-- <img src="/media/images/sssc.jpg" alt="" class="img-fluid"> -->
</header>