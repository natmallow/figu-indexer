<?php
$SECURITY->isLoggedIn();

use gnome\classes\DBConnection;
use gnome\classes\model\Publication;

$lang = lang();

$Publication = new Publication();

$pub_id = filter_input(INPUT_GET, 'id') ? filter_input(INPUT_GET, 'id') : "";
$publicationType = filter_input(INPUT_GET, 'pub_type');

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
    $publication_source =
    $notes = '';

function post()
{

    global $Publication;
    global $publicationType;
    global $lang;

    if (trim(filter_input(INPUT_POST, 'publication_id')) == '') {
        $_SESSION['actionResponse'] = "Publication abbreviation name cannot be empty";
        return;
    }

    if (filter_input(INPUT_POST, 'action') == 'add') {
        $id = $Publication->addPublication();
        $_SESSION['actionResponse'] = "Publication $id has been added!";
        header("Location: ./publications.php?lang=$lang");
        exit();
    } elseif (filter_input(INPUT_POST, 'action') == 'edit') {
        $id = $Publication->updatePublication();
        $_SESSION['actionResponse'] = "Publication $id has been Updated!";
        header("Location: ./publication.php?action=edit&id=$id&pub_type=$publicationType&lang=$lang");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    post();
}


if (!empty($pub_id)) {
    $publication = $Publication->getPublication($pub_id);
    extract($publication);
}

$publicationTypeInfo = (object) $Publication->getPublicationType($publicationType);

if (!isset($publicationTypeInfo->name)) {
    $_SESSION['actionResponse'] = "Publication catagory was not found";
    header("Location: ./publications_index.php");
}

?>
<!DOCTYPE html>
<html>

<head>
    <?php
    //  include __DIR__ . '../../includes/header.inc.php'; 
    ?>
    <?php include __DIR__ . '/../includes/head.inc.php'; ?>
    <link rel="stylesheet" href="../assets/jodit/jodit.min.css">
    <style>
        .decide {
            box-shadow: inset 0px 0px 0px 0px #e184f3;
            background: linear-gradient(to bottom, #c123de 5%, #a20dbd 100%);
            background-color: #c123de;
            border-radius: 7px 0px 0px 7px;
            display: inline-block;
            cursor: pointer;
            color: #ffffff;
            font-family: Arial;
            font-size: 13px;
            font-weight: bold;
            padding: 6px 12px;
            text-decoration: none;
        }

        .decide:hover {
            background: linear-gradient(to bottom, #a20dbd 5%, #c123de 100%);
            background-color: #a20dbd;
        }

        .decide:active {
            position: relative;
            top: 1px;
        }

        .decide-true {
            box-shadow: inset 0px 0px 0px 0px #e184f3;
            background: linear-gradient(to bottom, #007dc1 5%, #0061a7 100%);
            background-color: #007dc1;
            border-radius: 0px 7px 7px 0px;
            display: inline-block;
            cursor: pointer;
            color: #ffffff;
            font-family: Arial;
            font-size: 13px;
            padding: 6px 12px;
            text-decoration: none;
        }

        .decide-true:hover {
            background: linear-gradient(to bottom, #0061a7 5%, #007dc1 100%);
            background-color: #0061a7;
        }

        .decide-true:active {
            position: relative;
            top: 1px;
        }

        /** test enumeration change */
        .eNum::before {
            content: attr(id);
            color: var(--rgba-primary-0);
            font-weight: bold;
        }
    </style>

    <script src="../assets/js/indexer.js"></script>
    <script src="../assets/jodit/jodit.es2018.min.js"></script>
    <script src="../assets/jodit-custom/enumerate.js?version=4.1325"></script>
</head>

<body class="">
    <?php include __DIR__ . '../../includes/topnav.inc.php'; ?>
    <?php include '../includes/sidebar.inc.php'; ?>
    <main id="main" class="main">

        <?php include '../includes/title.inc.php'; ?>
        <div class="pagetitle">
            <h1>You are Editing Publication</h1>
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
                        Publication (<?= $pub_id ?>)
                    </li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section" id="wrapper">
            <div class="row">
                <div class="col-12">
                    <?php include '../includes/head-resp.inc.php'; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php if (!empty($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'add')) : ?>
                        <!-- onsubmit="myFunction()" -->
                        <form id="publicationForm" method="POST">
                            <input type="hidden" name="action" value="<?= $_GET['action'] ?>">
                            <input type="hidden" name="publication_id" value="<?= $publication_id ?>">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="$publication_id" class="form-label">Publication abbreviation name:</label>
                                    <input type="text" name="$publication_id" id="$publication_id" class="form-control" value="<?= $publication_id ?>" disabled />
                                </div>
                                <div class="col-8">
                                    <label for="publication_source" class="form-label">Publication Source (URL):</label>
                                    <div class="input-group">
                                        <input type="text" name="publication_source" id="publication_source" class="form-control" value="<?= $publication_source ?>" required minlength="20" maxlength="300" />
                                        <button class="btn btn-outline-secondary" type="button" id="copyUrlButton">Copy</button>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label for="" class="form-label">&nbsp;</label>
                                    <button id="openImporter" type="button" class="btn btn-primary d-block" style="width: 100%;">import
                                        publication</button>
                                </div>
                                <div class="col-8">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" maxlength="1990" class="form-control"><?= $notes ?></textarea>
                                    <div class="d-flex justify-content-end">
                                        <div id='notes-msg'>1990 characters left</div>
                                        <div id='lastChar'></div>
                                    </div>


                                </div>
                                <div class="col-4">

                                    <label for="is_ready" class="form-label d-block">Ready for indexing?</label>
                                    <div class="radio-scale">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" value='1' id="rad-yes-<?= $is_ready ?>" name="is_ready" <?= $is_ready == 1 ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="rad-yes-<?= $is_ready ?>">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" value='0' id="rad-no-<?= $is_ready ?>" name="is_ready" <?= $is_ready == 0 ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="rad-no-<?= $is_ready ?>">No</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- <label for="raw_html_summary">Publication Summary:</label>
                                <textarea name="raw_html_summary" form="publicationForm"
                                    id="raw_html_summary">< ?= $raw_html_summary ?></textarea> -->
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="raw_html">Publication text html:</label>
                                    <textarea name="raw_html" form="publicationForm" id="raw_html"><?= $raw_html ?></textarea>
                                </div>
                            </div>

                            <div class="row pt-5">
                                <div class="col-6 col-md-6">
                                    <div>
                                        <!-- <button onclick="reset()">Cancel</button> -->
                                        <a href="publications_index.php" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                                <div class="col-6 col-md-6">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Save Publication</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php else : ?>
                        Nothing Selected
                    <?php endif ?>
                </div>

            </div>
        </section>
    </main>







    <div id="importModal" class="modal" tabindex="-1" id="create-modal">
        <div id="importContent" class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div id="containerImport">
                    <!-- data inserted here -->
                </div>
            </div>
        </div>
    </div>
    <script>
        try {

            editor = Jodit.make('#raw_html', {
                buttons: [
                    ...Jodit.defaultOptions.buttons,
                    '\n',
                    nextDiscrepancy(),
                    '|',
                    highlightAbbreviationsPublication(),
                    '|',
                    enumerate(),
                    removeEnumerate(),
                    '|',
                    '---',
                    savePublication(),
                ]
            });


        } catch (err) {
            console.log(err, 'error bringing jodit online')
        }
    </script>
    <?php include __DIR__ . '/../includes/script.import.inc.php'; ?>
    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/../includes/footer.inc.php'; ?>
    <?php include __DIR__ . '/../includes/script.inc.scraper.php'; ?>


    <script>
        //  smell = function(){ alert('pigs') } 

        initModal('/gnome/indexer/publication_scraper.php?publication_id=<?= $publication_id ?>',
            'importModal',
            'openImporter',
            'closeModal',
            'containerImport',
            ['callAgain', 'initScraper']);



        function charCount() {
            var textEntered = document.getElementById('notes').value;
            var msg = document.getElementById('notes-msg');
            var counter = (2000 - (textEntered.length));
            msg.textContent = counter + ' characters left';
        }

        var el = document.getElementById('notes');
        el.addEventListener('keyup', charCount, false);
    </script>
    <script>
        document.getElementById('copyUrlButton').addEventListener('click', function() {
            var copyText = document.getElementById('publication_source');
            copyText.select();
            copyText.setSelectionRange(0, 99999); /* For mobile devices */
            document.execCommand('copy');

            // Optional: Change button text to indicate copying
            var originalText = this.innerHTML;
            this.innerHTML = 'Copied!';
            setTimeout(() => {
                this.innerHTML = originalText;
            }, 2000);
        });
    </script>


</body>

</html>