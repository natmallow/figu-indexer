<?php
$SECURITY->isLoggedIn();

use gnome\classes\model\Publication;

$Publication = new Publication();

$publicationTypes = $Publication->getPublicationTypes();

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
            <h1>Publications</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active">Publication Types</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
 
        <section class="section">
            <div class="row">
                <div class="col-12 d-flex justify-content-between mb-4">
                    <h3>Available Publications</h3>
                    <a href="./publications_index_edit.php?action=add" class="btn btn-primary">
                        Add Publication Ref
                    </a>
                </div>
            </div>
            <div class="row">
                <?php foreach ($publicationTypes as $key => $value) : ?>
                    <div class="col-6">
                        <a href="/gnome/indexer/publications.php?pub_type=<?= $value['publication_type_id'] ?>" data-toggle="tooltip" data-original-title="<?= $value['abbreviation'] ?>"><?= $value['name'] ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- <div class="row">
                            <div class="col-12">
                                <label for="publication_id">Publication abbreviation Name:</label>
                                <div class="contact-report" id="raw_html">< ?= // $raw_html ? ></div>
                            </div>
                        </div> -->
        </section>
    </main>



    <!-- The Modal  onscroll="myFunction()"-->
    <div id="importModal" class="modal">

        <!-- Modal content -->
        <div id="importContent" class="modal-content-basic">
            <span class="close-x" id="closeModal">&times;</span>
            <div class="box alt">
                <div id='containerImport'>

                </div>
            </div>
        </div>

    </div>
    <?php include __DIR__ . '/../includes/script.import.inc.php'; ?>
    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '../../includes/footer.inc.php'; ?>
    <script>
        initModal('publication_create.php?type=<?= $publicationType ?? '' ?>', 'create-modal', 'publication-creation', 'closeModal', 'containerImport',
            'section-image', []);
    </script>

</body>

</html>