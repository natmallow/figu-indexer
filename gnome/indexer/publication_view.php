<?php
$SECURITY->isLoggedIn();

use gnome\classes\model\Publication;



$Publication = new Publication();

$publication_id =
    $german =
    $english =
    $english_name =
    $german_name =
    $author =
    $date =
    $raw_html =
    $publication_type_id =
    $is_ready =
    $title =
    $notes = '';



$p_id = filter_input(INPUT_GET, 'id') ? filter_input(INPUT_GET, 'id') : "";

if ($p_id === "") {
    $_SESSION['actionResponse'] = "No Publication selected";
    header("Location: publications.php");
    die();
}



// Select Statements:
$publication = $Publication->getPublication($p_id);

extract($publication);

$publicationType = filter_input(INPUT_GET, 'pub_type');

$publicationTypeInfo = (object) $Publication->getPublicationType($publicationType);

if (!isset($publicationTypeInfo->name)) {
    $_SESSION['actionResponse'] = "Publication catagory was not found";
    header("Location: ./publications_index.php");
}


?>
<!DOCTYPE html>
<html>


<head>
    <?php include __DIR__ . '/../includes/head.inc.php'; ?>
    <style>
        #main {
            padding-right: 72px;
        }

        .contact-report {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-column-gap: 10px;
        }

        .contact-report>div:nth-child(even) {
            visibility: hidden;
        }
    </style>
</head>

<body class="">
    <?php include __DIR__ . '../../includes/topnav.inc.php'; ?>
    <?php include '../includes/sidebar.inc.php'; ?>
    <main id="main" class="main">


        <?php include '../includes/title.inc.php'; ?>

        <div class="pagetitle">
            <h1>Publication Rendered view</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/gnome/index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/publications_index.php">Publication Types</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/publications.php?pub_type=<?= $publicationType ?>"><?= $publicationTypeInfo->name ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        (<?= $publication_id ?>)
                    </li>
                </ol>
            </nav>
        </div>


        <section>
            <div class="row">
                <!-- <div class="col-4">
                    <button class="button small" onclick="hide('raw_html')">hide rotator</button>
                </div> -->
                <div class="col-12">
                    <!-- <label for="publication_id">Publication abbreviation Name:</label> -->
                    <div class="contact-report" id="raw_html"></div><?= $raw_html ?>
                </div>
            </div>
        </section>
    </main>




    <script>
        function hide(target) {
            const elem = document.querySelector(".contact-report > div:nth-child(even)");
            elem.style.visibility = 'hidden';
        }
    </script>

    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/../includes/footer.inc.php'; ?>

</body>

</html>