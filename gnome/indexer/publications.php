<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\Publication;

$lang = lang();

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
    $title =
    $is_ready =
    $notes = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (filter_input(INPUT_POST, 'action') == 'add') {

        $id = $Publication->addPublication();
        $_SESSION['actionResponse'] = "Publication $id has been added";
        header("Location: ./publications.php?lang=$lang");
        exit();
    }
}


$publicationType = filter_input(INPUT_GET, 'pub_type');

// Select Statements:
$publications = $Publication->getPublications($publicationType);

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
</head>

<body class="">
    <?php include __DIR__ . '../../includes/topnav.inc.php'; ?>
    <?php include '../includes/sidebar.inc.php'; ?>
    <main id="main" class="main">

        <?php include '../includes/title.inc.php'; ?>
        <div class="pagetitle">
            <h1>Available Publications</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/gnome/index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/publications_index.php">Publication Types</a>
                    </li>
                    <li class="breadcrumb-item active">
                        (<?= $publicationTypeInfo->name ?>)
                    </li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section" id="wrapper">
            <div class="col-12">
                <div class="row pb-3">
                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <h4><?= $publicationTypeInfo->name ?></h4>

                        <button id="publication-creation" class="btn btn-primary btn-lg">
                            Create Publication
                        </button>
                    </div>
                </div>
                <div class="row pb-3">
                    <div class="col-12 ">
                        <div class="legend">Legend:
                            <div class="ready"></div> Ready for indexing,
                            <div class="not-ready"></div> Needs work
                        </div>
                    </div>
                </div>

                <div class="row gx-2 gy-2">

                    <?php
                    foreach ($publications as $key => $value) : ?>

                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 pub-block <?= ($value['is_ready'] == 1) ? 'ready' : 'not-ready' ?>">
                            <div class="pub-name">
                                <?php if (trim($value['notes'] ?? '') != '') : ?>
                                    <a href="#">
                                        <span class="bi bi-exclamation-circle-fill" data-bs-toggle="tooltip" data-bs-original-title="<?= str_replace('"', '&quot;', $value['notes']); ?>"></span>
                                    </a>
                                <?php endif ?>
                                <a href="/gnome/indexer/publication_view.php?id=<?= $value['publication_id'] ?>&pub_type=<?= $publicationType ?>" class="">
                                    <?php echo $value['publication_id']; ?>
                                </a>
                            </div>
                            <a href="/gnome/indexer/publication.php?action=edit&id=<?= $value['publication_id'] ?>&pub_type=<?= $publicationType ?>" class="update-btn">edit</a>
                        </div>

                    <?php endforeach; ?>
                </div>

            </div>
        </section>
    </main>



    <div class="modal" tabindex="-1" id="create-modal">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div id="containerImport">
                    <!-- data inserted here -->
                </div>
            </div>
        </div>
    </div>





    <?php include __DIR__ . '/../includes/script.import.inc.php'; ?>
    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/../includes/footer.inc.php'; ?>




    <script>
        initModal('publication_create.php?type=<?= $publicationType ?>',
            'create-modal',
            'publication-creation',
            'closeModal',
            'containerImport',
            ['listenerBlock']);
    </script>
</body>
</html>