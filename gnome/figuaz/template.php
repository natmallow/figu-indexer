<?php
$SECURITY->isLoggedIn();

use gnome\classes\MessageResource;
use gnome\classes\model\Template;

$lang = lang();
$action = action();

$Template = new Template();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $Template->save();
}

if (!empty(filter_input(INPUT_GET, 'id'))) {

    $id = filter_input(INPUT_GET, 'id');

    $template = $Template->get($id);
    extract($template[0]);

    $pdoc = null;
}
?>

<html>


    <head>
        <!-- <link rel="stylesheet" type="text/css" href="assets/content-tools.min.css"> -->
        <?php include __DIR__ . '/includes/header.inc.php'; ?>
        <link rel="stylesheet" href="assets/jodit/jodit.min.css">
        <script src="assets/jodit/jodit.js"></script>
    </head>

    <body class="">
        <div id="wrapper">
            <div id="main">
                <div class="inner">
                    <?php include '../includes/title.inc.php'; ?>
                    <section style="padding-block-end: 120px;">
                        <header class="main">
                            <h2>You are editing <?= $Template->pageTitle ?> <small>(in)</small> <span style="color:darkorange;"><?php echo ($lang == 'en') ? 'English' : 'Spanish' ?></span></h2>
                        </header>
                        <div class="row gtr-200">

                            <div class="col-12 col-12-medium">
                                <?php
                                if ($_SESSION['actionResponse'] != '') {
                                    echo "<div class='notification'>$_SESSION[actionResponse]</div>";
                                }

                                $_SESSION['actionResponse'] = '';
                                ?>
                            </div>
                        </div>
                        <?php if ($action != '' && ($action == 'edit' || $action == 'add')) : ?>
                            <form id="articleFrom" method="POST">
                                <div class="row gtr-200">


                                    <div class="col-12 col-12-medium col-12-small">
                                        <input type="hidden" name="action" value="<?= $action ?>">
                                        <input type="hidden" name="email_template_id" value="<?= $email_template_id ?>">
                                        <label for="name">Template Name:</label>
                                        <input type="text" name="name" id="title" value="<?= htmlentities($name) ?>" required minlength="4" maxlength="250">
                                    </div>


                                    <div class="col-12 col-12-medium col-12-small">
                                        <label for="description_html">Content:</label>

                                        <textarea rows="4" cols="20" name="description_html" form="articleFrom" id="description_html"><?= $description_html ?></textarea>

                                                                    <!-- <div id="editor"><?= $description_html ?></div> -->
                                    </div>

                                    <div class="col-12">
                                        <label for="link_download_internal">Link to downloadable file (pdf, doc, zip, etc):</label>
                                    </div>
                                    <div class="col-2 col-2-medium col-3-small">
                                        <button id="openFileModal" data-only="file" type="button">Attach file</button>
                                    </div>
                                    <div class="col-12 col-12-medium">
                                        <hr><br>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div style="text-align: left">
                                            <a href="/gnome/templates.php" class="button">Cancel</a>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div style="text-align: right">
                                            <button type="submit">
                                                <?php echo ($action === 'add') ? 'Save' : 'Update'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php else : ?>
                            <div class="row gtr-200">
                                <div class="col-12 col-12-medium">
                                    Invalid action
                                </div>
                            </div>
                        <?php endif ?>
                    </section>
                </div>
            </div>
            <?php include 'includes/sidebar.inc.php'; ?> 
        </div>
    </div>
</div>
<!-- The Modal -->
<div id="filesModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content-basic">
        <span class="close-x" id="closeModal">&times;</span>
        <div class="box alt">
            <div class="row gtr-50 gtr-uniform" id='modalData'>

            </div>
        </div>
    </div>

</div>
<?php include __DIR__ . '/../includes/script.image.inc.php'; ?>
<?php include __DIR__ . '/../includes/script.nav.inc.php'; ?>
<script>
        function factory() {
            initModal('filesModal', 'openModal', 'closeModal', 'modalData', 'section-image', ['imageSelectHandler']);
            initModal('filesModal', 'openFileModal', 'closeModal', 'modalData', 'link_download_internal', ['docSelectHandler']);
        }

        window.onload = factory();
</script>
<script>
    var editor = new Jodit('#description_html', {
        filebrowser: {
            ajax: {
                url: 'assets/connector/index.php'
            },
            uploader: {
                url: 'assets/connector/index.php?action=fileUpload',
            }
        }
    });
    ;
</script>
</body>

</html>