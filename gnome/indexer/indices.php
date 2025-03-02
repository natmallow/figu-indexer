<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\Indices;

$lang = lang();

$Indices = new Indices();


$indices_id = '';
$name = '';
$description_html = '';
$highlight_color = '';
$text_color = '';
$optionalFields = [];
$metaFields = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (filter_input(INPUT_POST, 'action') == 'add') {
        $id = $Indices->addIndex();
        $Indices->saveIndexPermission($id, $_SESSION['username'], '1', '1', '1', '1');
        header("Location: ./indices.php?lang=$lang");
        exit();
    }
}

list($indices, $paginator) = $Indices->getIndices();

// $permissions = $Indices->getPermission();

// individual
// $section = null;
// if (!empty($_GET['id'])) {
//     $index =  $Indices->getIndex($_GET['id']);
//     extract($index);
//     $optionalFields = $Indices->getOptionalFields($_GET['id']);
//     $metaFields =  $Indices->getMetaFields($_GET['id']);
// }

?>
<!DOCTYPE html>
<html>

<head>
    <?php include __DIR__ . '/../includes/head.inc.php'; ?>
    <link rel="stylesheet" href="../assets/jodit/jodit.min.css">
    <script src="../assets/jodit/jodit.min.js"></script>
</head>

<body class="wrapper">
    <?php include __DIR__ . '../../includes/topnav.inc.php'; ?>
    <?php include '../includes/sidebar.inc.php'; ?>
    <main id="main" class="main">

        <?php include '../includes/title.inc.php'; ?>

        <div class="pagetitle">
            <h1>Indices</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/gnome/index.php">Home</a></li>
                    <li class="breadcrumb-item active">Indices</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <!-- <header class="py-3 mb-4 border-bottom">
                <div class="col-12">
                    <span class="fs-4">Indices</span>
                </div>
            </header> -->
            <div class="row">
                <div class="col-12">
                    <?php include '../includes/head-resp.inc.php'; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 d-flex justify-content-between mb-4">
                    <h3>Available Indices</h3>
                    <a href="./index_detail.php?action=add" class="btn btn-primary">
                        Create New Index
                    </a>
                </div>
                <div class="col-12 mb-3">
                    <div class="d-flex justify-content-start align-items-center gap-2">
                        <?= $paginator->display_pages(); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php foreach ($indices as $index => $row) : ?>

                    <div class="col-md-6">
                        <div class="card d-flex flex-column --index">
                            <div class="card-body flex-grow-1">
                                <div class="card-header pb-1 d-flex justify-content-between gap-2">
                                    <!-- <strong>Index title and highlight format:</strong> -->
                                    <!-- <span class="smooth-span" data-tooltip="<?= is_null($row["name"]) ? 'needed' : $row["name"]; ?>"><?= is_null($row["name"]) ? 'needed' : $row["name"]; ?></span> -->
                                    <span class="--smooth fs-5 smooth-span" 
                                         style="color:<?= $row["text_color"] ?>; background-color:<?= $row["highlight_color"] ?>;" 
                                         data-bs-toggle="tooltip" data-bs-original-title="Index title and highlight format" >
                                        <?= is_null($row["name"]) ? 'needed' : $row["name"]; ?>
                                    </span>
                                    <a href="./indexlinks.php?index_id=<?= $row["indices_id"] ?>&lang=<?= $lang ?>" class="btn btn-success enf-len">Continue Indexing</a>
                                </div>
                                <h6 class="text-muted">Author: <?= $row["ownerName"] ?></h6>
                                <div class="index-desc">
                                    <strong>Description:</strong><br>

                                    <div class="container">
                                        <div class="text-container" id="textContainer<?= $index ?>">
                                            <?= $row["description_html"] ?>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button class="btn btn-link btn-sm d-none" onclick="toggleText(<?= $index ?>)" id="readMoreButton<?= $index ?>">Read More</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-actions">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <a href="./index_detail.php?index_id=<?= $row["indices_id"] ?>&action=edit&lang=<?= $lang ?>" class="btn btn-primary">Edit Index</a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach ?>
            </div>



            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-start align-items-center gap-2">
                        <?= $paginator->display_pagination(); ?>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <!-- The Modal -->
    <div id="imagesModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content-basic">
            <span class="close-x" id="closeModal">&times;</span>
            <div class="box alt">


                <div class="row gtr-50 gtr-uniform" id='container-image'>

                </div>

            </div>
        </div>

    </div>
    <?php include __DIR__ . '/../../includes/script.image.inc.php'; ?>
    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '../../includes/footer.inc.php'; ?>

    <script>

            function toggleText(index) {
                var textContainer = document.getElementById("textContainer" + index);
                var button = document.getElementById("readMoreButton" + index);

                if (textContainer.style.maxHeight === "4em" || textContainer.style.maxHeight === "") {
                    textContainer.style.maxHeight = textContainer.scrollHeight + "px";
                    button.textContent = "Read Less";
                } else {
                    textContainer.style.maxHeight = "4em";
                    button.textContent = "Read More";
                }
            }

            // Function to initially check the height of each text container
            function checkContentHeight() {
                var containers = document.querySelectorAll('.text-container');
                containers.forEach((container, index) => {
                    var button = document.getElementById("readMoreButton" + index);
                    if (container.scrollHeight > container.offsetHeight) {
                        // If content height is more than container height, show the button
                        button.classList.remove('d-none');
                    }
                });
            }
            checkContentHeight();
       
        // Run the check after the page has loaded
    </script>

</body>

</html>