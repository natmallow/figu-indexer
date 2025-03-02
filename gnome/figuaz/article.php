<?php

$SECURITY->isLoggedIn();

$Article = gnome\classes\model\Article::factory();
$Section = gnome\classes\model\Section::factory();


$id_articles = '';
$title = '';
$summary = '';
$content_html = '';
$image = '';
$image_description = '';
$is_published = 0;
$is_external_only = 0;
$link_download_internal = '';
$link_external = '';
$read_counter = '';
$author = '';
$original_publication_date = '';
$sections = '';
$top_title = '';
// $updated_by;
// $created_by;
// $updated_date;
// $created_date;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit') {

        $Article->updateArticleAndBody(lang());
        $Article->updateLinkTable($_POST['id_articles'], $_POST['sections']);
        $_SESSION['actionResponse'] = 'Edit Complete!';
    } elseif ($_POST['action'] == 'add') {

        $Article->addArticleAndBody(lang());
        $Article->updateLinkTable($insertId, $_POST['sections']);
        $_SESSION['actionResponse'] = $_POST['title'] . 'Has Been Added';
    }
    header("Location: ./articles.php?lang=" . lang());
    exit();
}

$sections = $Section->getSectionsWithChildArticles();

// Select Statements:
if (!empty($_GET['id'])) {
    $article = $Article->getArticleFull($_GET['id'], lang());
    extract($article[0]);
    $id_articles = $id_articles_primary;
}

?>
<!DOCTYPE html>
<html>

<head>

    <!-- <link rel="stylesheet" type="text/css" href="assets/content-tools.min.css"> -->
    <?php include __DIR__ . '/includes/head.inc.php'; ?>
    <link rel="stylesheet" href="assets/jodit/jodit.min.css">
    <script src="assets/jodit/jodit.min.js"></script>

</head>

<body class="">
    <?php include __DIR__ . '/includes/topnav.inc.php'; ?>
    <?php include 'includes/sidebar.inc.php'; ?>
    <main id="main" class="main">


        <?php include 'includes/title.inc.php'; ?>

        <section class="section">
            <header class="py-3 mb-4 border-bottom">
                <div class="d-flex flex-wrap justify-content-center">
                    <div class="col-12">
                        <span class="fs-4">You are editing <small>(in)</small> <span style="color:darkorange;"><?php echo (lang() == 'en') ? 'English' : 'Spanish' ?></span></span>
                    </div>
                </div>
            </header>

            <?php include 'includes/head-resp.inc.php'; ?>

            <?php if (!empty($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'add')) : ?>
                <form id="articleFrom" method="POST">
                    <!-- hidden fields -->
                    <input type="hidden" name="action" value="<?= $_GET['action'] ?>">
                    <input type="hidden" name="id_articles" value="<?= $id_articles ?>">

                    <div class="container-fluid">
                        <div class="row">
                            <?php if ($_GET['action'] == 'edit') : ?>
                                <div class="col-mb-12 mb-3">
                                    <label for="primary_title">Primary Title <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" title="The 'Primary Title' is the used for referencing the article across languages."></i> :</label>
                                    <div class="input-group">
                                        <div class="input-group-text">
                                            <input class="form-check-input cbk" data-bs-toggle="tooltip" title="Edit?" type="checkbox" onclick="var d = document.getElementById('primary_title'); d.disabled = d.disabled ? false : true" id="is_primary_title">
                                        </div>
                                        <input type="text" name="primary_title" id="primary_title" class="form-control" disabled value="<?= htmlentities($primary_title) ?>" required minlength="4" maxlength="250">
                                    </div>
                                </div>
                            <?php endif ?>

                            <div class="col-mb-12 mb-3">
                                <label for="title" class="form-label">Title:</label>
                                <input type="text" name="title" id="title" class="form-control" value="<?= htmlentities($top_title) ?>" required minlength="4" maxlength="250">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="author" class="form-label">Author:</label>
                                <input type="text" name="author" id="author" class="form-control" value="<?= $author ?>" required minlength="4" maxlength="150">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="original_publication_date">Date of Original:</label>
                                <input type="datetime-local" name="original_publication_date" id="original_publication_date" class="form-control" value="<?= $original_publication_date ?>" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="" id="preview-container">
                                    <?php
                                    $dnone = '';
                                    if (trim($image) == '') :
                                        $dnone = 'd-none';
                                    ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="190px" viewBox="0 0 16 16" id="default-image">
                                            <path d="m 4 1 c -1.644531 0 -3 1.355469 -3 3 v 1 h 1 v -1 c 0 -1.109375 0.890625 -2 2 -2 h 1 v -1 z m 2 0 v 1 h 4 v -1 z m 5 0 v 1 h 1 c 1.109375 0 2 0.890625 2 2 v 1 h 1 v -1 c 0 -1.644531 -1.355469 -3 -3 -3 z m -5 4 c -0.550781 0 -1 0.449219 -1 1 s 0.449219 1 1 1 s 1 -0.449219 1 -1 s -0.449219 -1 -1 -1 z m -5 1 v 4 h 1 v -4 z m 13 0 v 4 h 1 v -4 z m -4.5 2 l -2 2 l -1.5 -1 l -2 2 v 0.5 c 0 0.5 0.5 0.5 0.5 0.5 h 7 s 0.472656 -0.035156 0.5 -0.5 v -1 z m -8.5 3 v 1 c 0 1.644531 1.355469 3 3 3 h 1 v -1 h -1 c -1.109375 0 -2 -0.890625 -2 -2 v -1 z m 13 0 v 1 c 0 1.109375 -0.890625 2 -2 2 h -1 v 1 h 1 c 1.644531 0 3 -1.355469 3 -3 v -1 z m -8 3 v 1 h 4 v -1 z m 0 0" fill="#2e3434" fill-opacity="0.34902" />
                                        </svg>
                                    <?php endif; ?>
                                    <img src="/media/<?= $image ?>" class="img-fluid <?= $dnone ?>" id="prev-image">
                                </div>
                            </div>

                            <div class="col-md-9 mb-3">
                                <label for="">Path</label>
                                <input type="text" name="image" class="form-control mb-3" id="section-image" value="<?= $image ?>" maxlength="200" readonly>
                                <label for="image_description">Image Description:</label>
                                <input type="text" name="image_description" id="image_description" class="form-control mb-3" value="<?= $image_description ?>" maxlength="400">
                                <label for="selectImage">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary" id="selectImage" data-bs-toggle="tooltip" title="Select an image for the Article">
                                        <i class="bi bi-images"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="content_html" class="form-label">Content:</label>
                                <textarea class="form-control" rows="4" cols="20" name="content_html" form="articleFrom" id="content_html"><?= $content_html ?></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="link_external">Link to original article :</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" class="cbk" name="is_external_only" id="is_external_only" value="1" <?php if ($is_external_only == '1') echo "checked='checked'"; ?>>
                                        <span class="ps-2">Use External link Only</span>
                                    </div>
                                    <input type="url" class="form-control" name="link_external" id="link_external" maxlength="400" value="<?= $link_external ?>">
                                </div>
                            </div>
                       
                        <div class="col-md-6 mb-3">
                            <label for="">Attach this Article to Section(s) <i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" title="Use (Ctrl) to select multiple or to deselect"></i> :</label>
                            <select name="sections[]" id="sections" class="form-select" style="height: 210px;" aria-label="multiple select example" multiple size="4" style="height: auto;">
                                <?php for ($i = 0; $i < count($sections); $i++) : ?>
                                    <option value="<?= $sections[$i]["id_sections"] ?>" <?php if ($sections[$i]["is_selected"]) echo "selected='selected'"; ?>>
                                        <?= $sections[$i]["name"] ?>
                                    </option>
                                <?php endfor ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="summary">Summary</label>
                            <textarea name="summary" id="summary" form="articleFrom"><?= $summary ?></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="link_download_internal">Link to downloadable file (pdf, doc, zip, etc):</label>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="input-group">
                                <button type="button" id="openFileModal" class="btn btn-outline-secondary" data-only="file">Attach file</button>
                                <input type="text" class="form-control" name="link_download_internal" readonly id="link_download_internal" maxlength="400" value="<?= $link_download_internal ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-6 mb-3">
                            <div class="text-center alert alert-success" role="alert">
                                <input type="checkbox" class="cbk" name="is_published" id="is_published" value="1" <?php if ($is_published == '1') echo "checked='checked'"; ?>>
                                <label for="is_published"> Publish this article</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3 d-flex justify-content-between">
                            <a href="/gnome/articles.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>

            <?php else : ?>
                Nothing Selected
            <?php endif ?>
        </section>
    </main>
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
    <?php include __DIR__ . '/includes/footer.inc.php'; ?>
    <script>
        function factory() {
            initModal('filesModal', 'selectImage', 'closeModal', 'modalData', 'section-image', ['imageSelectHandler']);
            initModal('filesModal', 'openFileModal', 'closeModal', 'modalData', 'link_download_internal', ['docSelectHandler']);
        }

        window.onload = factory();
    </script>
    <script>
        var editor = new Jodit('#content_html', {
            editorCssClass: 'default-jodit',
            filebrowser: {
                ajax: {
                    url: 'assets/connector/index.php'
                },
                uploader: {
                    url: 'assets/connector/index.php?action=fileUpload'
                }
            }
        });
        try {
            new Jodit('#summary', {
                editorCssClass: 'default-jodit',
                // toolbar: false,
                removeButtons: ['dots', 'fullsize', 'left', 'brush', 'ul', 'underline', 'ol', 'fontsize', 'paragraph', 'copy', 'paste', 'source', 'align', 'undo', 'redo',
                    'color', 'strikethrough', 'eraser', 'font', 'classSpan', 'lineHeight', 'superscript', 'subscript',
                    'file', 'image', 'video', 'speechRecognize', 'spellcheck', 'cut', 'selectall', 'copyformat', 'hr', 'table', 'link', 'symbols'
                ],
                buttons: 'bold,italic'
            });
        } catch (err) {}
    </script>
</body>

</html>