<?php

use gnome\classes\DBConnection;

$lang = lang();
$menuResult = gnome\classes\model\Section::factory()->getSections($lang);
$sideBarArticles = gnome\classes\model\Article::factory()->getSideBarArticles($lang);
$sidebarLinks = '';


foreach ($sideBarArticles as $key => $row) {
    $urlTitle = str_replace(' ', '-', htmlentities($row['title']));

    if ($row['is_external_only'] != '1') {
        $sidebarLinks .= "<article>
                <a href=\"/$lang/article/$row[id_articles]/$urlTitle\" class=\"image\">
                    <img src=\"media$row[image]\" alt=\"$urlTitle\" />
                </a>
                <h5 style=\"text-align: center\">" . htmlentities($row['title']) . "</h5>
                </article>";
    } else {
        $sidebarLinks .= "<article>
        <a href=\"/$lang/$row[link_external]\" target=\"_blank\" class=\"image\">
            <img src=\"media$row[image]\" alt=\"$urlTitle\" />
        </a>
        <h5 style=\"text-align: center\">" . htmlentities($row['title']) . "</h5>
        - external link
        </article>";
    }
}


?>
<?php include_once "meditation.inc.php"; ?>


<div id="sidebar">
    <div class="inner">
        <section id="search" class="alt">
            <form method="post" action="#"><input type="text" name="query" id="query" placeholder="Search" /></form>
        </section>
        <nav id="menu">
            <!-- <header class="major">
                        <h2>Menu</h2>
                    </header> -->
            <ul>
                <li><a href="/<?= $lang ?>/">Homepage</a></li>
                <?php

                $htmlBlock = '';
                $parentArray = [];
                $childArray = [];

                foreach ($menuResult as $row) {
                    if ($row["id_parent"] != 0) {
                        $childArray[$row["id_parent"]][] = $row;
                    } else {
                        $parentArray[$row["id_sections"]] = $row;
                    }
                }

                foreach ($parentArray as $row) {

                    // search child array 
                    if (array_key_exists($row['id_sections'], $childArray)) {
                        $htmlBlock .= '<li><span class="opener">' . $row['name'] . '</span>
                                        <ul>';
                        foreach ($childArray as $key) {
                            foreach ($key as  $cRow) {
                                if ($cRow['id_parent'] == $row['id_sections']) {
                                    $htmlBlock .=  "<li><a href=\"/$lang/sections/$cRow[id_sections]/" . strtolower(str_replace(' ', '-', strip_tags($cRow['name']))) . "\">$cRow[name]</a></li>";
                                }
                            }
                        }
                        $htmlBlock .=     '</ul>
                                    </li>';
                    } else {
                        $htmlBlock .= "<li><a href=\"/$lang/sections/$row[id_sections]/" . strtolower(str_replace(' ', '-', strip_tags($row['name']))) . "\">$row[name]</a></li>";
                    }
                }

                ?>
                <!-- <li><a href="/sections/<?= $row["id_sections"] . '/' . $urlName = strtolower(str_replace(' ', '-', strip_tags($row['name']))) ?>"><?= $row["name"] ?></a></li> -->
                <?php
                echo $htmlBlock;
                ?>

                <!-- <li><a href="generic.php">What's New</a></li>
                <li><a href="elements.php">Presentations</a></li> -->
                <!-- <li><a href="">zietguist</a>
                        <li><span class="opener">Submenu</span>
                            <ul>
                                <li><a href="#">Lorem Dolor</a></li>
                                <li><a href="#">Ipsum Adipiscing</a></li>
                                <li><a href="#">Tempus Magna</a></li>
                                <li><a href="#">Feugiat Veroeros</a></li>
                            </ul>
                        </li>
                        <li><a href="#">Etiam Dolore</a></li>
                        <li><a href="#">Adipiscing</a></li>
                        <li><span class="opener">Another Submenu</span>
                            <ul>
                                <li><a href="#">Lorem Dolor</a></li>
                                <li><a href="#">Ipsum Adipiscing</a></li>
                                <li><a href="#">Tempus Magna</a></li>
                                <li><a href="#">Feugiat Veroeros</a></li>
                            </ul>
                        </li>
                        <li><a href="#">Maximus Erat</a></li>
                        <li><a href="#">Sapien Mauris</a></li> 
                <li><a href="#">About Us</a></li>-->
            </ul>


        </nav>
        <section>
            <!-- <header class="major">
                        <h2>Ante interdum</h2>
                    </header> -->
            <div class="mini-posts">
                <?= $sidebarLinks ?>
                <!-- <article><a href="article/2/Billy-Eduard-Albert-Meier---BEAM" class="image"><img src="media/images/beam.jpg" alt="" /></a>
                    <h5 style="text-align: center">"Billy" Eduard Albert Meier - BEAM</h5>
                </article>
                <article><a href="#" class="image"><img src="media/images/samjase-silver-star-center.png" alt="" /></a>
                    <h5 style="text-align: center">FIGU in a Nutshell</h5>
                </article>
                <article><a href="#" class="image"><img src="media/images/meditation_symbol.png" alt="" /></a>
                    <h5 style="text-align: center">Salome Peace Meditation</h5>
                </article>
                <article><a href="#" class="image"><img src="media/images/talmud-jmmanuel.jpg" alt="" /></a>
                    <h5 style="text-align: center">The true teaching of Jmmanuel AKA Jesus <br>by - Judas Ischkerioth</h5>
                </article> -->

            </div>
            <!-- <ul class="actions">
                     <li><a href="#" class="button">More</a></li>
                 </ul> -->
        </section>
        <section>
            <header class="major">
                <h2>Peace Meditation</h2>
            </header>
            <div id="peace_times_box">
                <p>
                   <strong><?php print 'Saturday, ' . $nMonth . ' ' . $date1 ?></strong> <br>
                    <span>Next Meditation (<span class="meditation" id='medTime1'>Meditation</span>) 5:30 PM UTC</span><br />
                    <span>Next Meditation (<span class="meditation" id='medTime2'>Meditation</span>) 7:00 PM UTC</span>
                </p>
                <p>
                <strong><?php print 'Sunday, ' . $nMonth . ' ' . $date2  ?> </strong> <br>
                    <span>Next Meditation (<span class="meditation" id='medTime3'>Meditation</span>) 7:00 PM UTC</span>
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
        </section>
        <section>
            <header class="major">
                <h2>Get in touch</h2>
            </header>
            <p>
                Currently we hold online spiritual (Creational Energy) meetings on the 4th Saturday of each month. 
                If you would like to attend and/or participate please email us at the address below.
            </p>
            <ul class="contact">
                <li class="icon solid fa-envelope"><a href="mailto:arizonaforfigu@protonmail.com" >arizonaforfigu@protonmail.com</a></li>
                <!-- <li class="icon solid fa-phone">(000) 000-0000</li> -->
                <li class="icon solid fa-home">Po box 3184<br>
                    Flagstaff, AZ, 86003
                </li>
            </ul>
        </section>
        <footer id="footer">
            <p class="copyright">&copy; Untitled. All rights reserved.</p>
        </footer>
    </div>
</div>