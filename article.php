<?php
use gnome\classes\model\Article as Article;

$id_articles = $_GET['id_articles'];

$lang = lang();

$Article = new Article();
$article = $Article->getArticle($id_articles, $lang);

extract($article);

// title`,
// content_html`,
// image`,
// summary`,
// is_published`,
// link_internal`,
// link_external`,
// created_by`,
// updated_by`,
// created_date`,
// updated_date`,
// read`,
// sections_id_sections`

?>

<html>


<head>

    <base href="/">
    <?php include 'includes/header.inc.php'; ?>

</head>

<body>
    <div id="wrapper">
        <div id="main">
            <div class="inner">
                <?php include 'includes/title.inc.php'; ?>
                <section>
                    <header class="main">
                        <h1>
                            <?= $title ?>
                        </h1>
                    </header>
                    <span class="image main">
                        <img src="media/<?= $image ?>" alt="">
                    </span>
                    <?php if ($link_download_internal) : ?>
                    <div class="downloadable">
                        <a href="media/<?= $link_download_internal ?>" rel="nofollow" target="_blank">Download
                            article</a>
                    </div>
                    <?php endif ?>
                    <?php if ($link_download_internal) : ?>
                    <div class="downloadable">
                        <a href="media/<?= $link_external ?>" rel="nofollow" target="_blank">Link to Original
                            article</a>
                    </div>
                    <?php endif ?>
                    <?= $content_html ?>
                </section>
            </div>
        </div>
        <?php include 'includes/sidebar.inc.php'; ?>
    </div>
    <?php include 'includes/script.nav.inc.php'; ?>
</body>

</html>