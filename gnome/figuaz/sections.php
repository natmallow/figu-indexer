<?php

$SECURITY->isLoggedIn();

$Section = new gnome\classes\model\Section();

$lang = lang();


$name = '';
$idsection = '';
$description = '';
$is_active = 0;
$image = '';
$image_description = '';
$summary = '';
$id_sections = '';
$id = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if ($_POST['action'] == 'edit') {

        gnome\classes\model\Section::factory()->updateSection();
        $_SESSION['actionResponse'] = 'Edit Complete!';
        header("Location: ./sections.php?lang=" . $_POST['lang']);
        exit();
    } elseif ($_POST['action'] == 'add') {

        gnome\classes\model\Section::factory()->addSection();
        $_SESSION['actionResponse'] = $_POST['name'] . 'Has Been Added';
        header("Location: ./sections.php?lang=" . $_POST['lang']);
        exit();
    }
}

$sections = gnome\classes\model\Section::factory()->getSectionsAndBody($lang);

// individual
if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $section = gnome\classes\model\Section::factory()->getSectionAndBody($_GET['id'], $lang);
    extract($section);
}

?>
<!doctype html>
<html>

<head>
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
                <div class="col-12">
                    <span class="fs-4">You are editing Articles <small>(in)</small> <span style="color:darkorange;"><?php echo (lang() == 'en') ? 'English' : 'Spanish' ?></span></span>
                </div>
            </header>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-2 fw-bold">Available Sections</span>
                    <a href="/gnome/sections.php?action=add" class="btn btn-primary btn-sm"> Create New </a>
                </div>
                <div class="col-md-12">
                    <?php include 'includes/head-resp.inc.php'; ?>
                </div>
                <div class="col-md-3">

                    <?php foreach ($sections as $row) :
                        $selected = $row['id_sections'] == $id ? '--selected' : '';
                    ?>
                        <div class="card mb-2 --card <?= $selected ?>">
                            <div class="card-body --sm">
                                <div class="text-nowrap overflow-hidden text-truncate mt-2 fw-bold">
                                    <?= is_null($row["top_name"]) ? 'needed' : $row["top_name"]; ?>
                                </div>
                                <h6 class="card-subtitle small">
                                    (<?= $row["name"] ?>)</h6>
                                <div class="card-text mb-3">
                                    <div>
                                        <input onclick="toggleGen('/gnome/ajax.php?id=<?= $row['id_sections'] ?>&action=toggletoHomepage')" id="is_on_homepage<?= $row['id_sections'] ?>" type="checkbox" <?php if ($row["is_on_homepage"] == '1') echo "checked='checked'"; ?>>
                                        <label for="is_on_homepage<?= $row["id_sections"] ?>">Add to homepage</label>
                                    </div>
                                    <div class="text-end">
                                        <a href="/gnome/sections.php?id=<?= $row["id_sections"] ?>&action=edit&lang=<?= $lang ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>

                </div>
                <div class="col-md-9">

                    <?php if (!empty($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'add')) : ?>
                        <form id="sectionForm" method="POST">
                            <input type="hidden" name="action" value="<?= $_GET['action'] ?>">
                            <input type="hidden" name="lang" value="<?= $lang ?>">
                            <input type="hidden" name="id_sections" value="<?= $primary_id_sections ?>">

                            <div class="mb-3">
                                <label for="fname" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?= $name ?>" required minlength="4" maxlength="40">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" rows="4" cols="50" name="description" form="sectionForm" id="description"><?= $description ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
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

                                <div class="col-md-8 mb-3">
                                    <label for="">Path</label>
                                    <input type="text" name="image" class="form-control mb-3" id="section-image" value="<?= $image ?>" maxlength="200" readonly>
                                    <label for="image_description">Image Description</label>
                                    <input type="text" name="image_description" id="image_description" class="form-control mb-3" value="<?= $image_description ?>" maxlength="400">
                                    <label for="selectImage">&nbsp;</label>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-primary" id="selectImage" data-bs-toggle="tooltip" title="Select an image for the Article">
                                            <i class="bi bi-images"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <div class="mb-3">
                                <label for="summary" class="form-label">Summary</label>
                                <textarea class="form-control" name="summary" id="summary" form="sectionForm"><?= $summary ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="id_parent" class="form-label">If sub-menu select parent</label>
                                <select name="id_parent" class="form-select" aria-label="Default select example">
                                    <option value="0">Not a sub-menu</option>
                                    <?php foreach ($sections as $row) : ?>
                                        <option value="<?= $row["id_sections"] ?>" <?php if ($id_parent ==  $row["id_sections"]) echo "selected='selected'"; ?>><?= $row["name"] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 mb-3">
                                    <div class="text-center alert alert-success" role="alert">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" <?php if ($is_active == '1') echo "checked='checked'"; ?>>
                                        <label for="is_active">Make this section active</label>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12 mb-3 d-flex justify-content-between">
                                    <button onclick="reset()" class="btn btn-secondary">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    <?php else : ?>
                        <div class="row">
                            <div class="col-md-12 mb-3 text-center">
                                Nothing Selected
                            </div>
                        </div>
                    <?php endif ?>
                </div>

            </div>
        </section>
    </main>




    <div class="modal" tabindex="-1" id="imagesModal">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Selector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id='container-image'>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>





    <?php include __DIR__ . '/../includes/script.image.inc.php'; ?>
    <?php include __DIR__ . '/../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/includes/footer.inc.php'; ?>

    <script>






        function factory() {
            initModal('imagesModal', 'selectImage', 'closeModal', 'container-image', 'section-image', ['imageSelectHandler']);
        }

        window.onload = factory();

        try {
            var editor = new Jodit("#description", {
                editorCssClass: 'default-jodit',
                "buttons": "source,bold,|,underline,italic,|,|,,|,font,fontsize,brush,|,,undo,redo,selectall,cut,copy,paste,copyformat"
            });
        } catch (err) {}

        try {
            var editor2 = new Jodit("#summary", {
                editorCssClass: 'default-jodit',
                "buttons": "source,bold,|,underline,italic,|,|,,|,font,fontsize,brush,|,,undo,redo,selectall,cut,copy,paste,copyformat"
            });
        } catch (err) {}
    </script>
</body>

</html>