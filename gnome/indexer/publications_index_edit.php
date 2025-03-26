<?php
$SECURITY->isLoggedIn();

use gnome\classes\model\Publication;

$Publication = new Publication();

$publicationTypes = $Publication->getPublicationTypes();

// action should be edit | add | saved
$action = filter_input(INPUT_GET, 'action', FILTER_VALIDATE_EMAIL);

$action = empty($_GET['action']) ? 'edit' : 'add';


$nameErr = $abbreviationErr = "";
$name = $abbreviation = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = sanitize(filter_input(INPUT_POST, 'name'));
    $abbreviation = sanitize(filter_input(INPUT_POST, 'abbreviation'));

    $resp = $Publication->addPublicationType($name, $abbreviation);

    // Add your database insertion logic here if validation passes
    if ($resp) {
        redirectToCurrentPage();
    } 

    // $nameErr = 'toggleGenErr()';
    // else there is an error so trigger JS
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
            <h1>Publications</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="publications_index.php">Publication Types</a></li>
                    <li class="breadcrumb-item active">Publication action (<?= $action ?>)</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <?php include '../includes/head-resp.inc.php'; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 d-flex justify-content-between mb-4">
                    <h3>Available Publications</h3>
                    <a href="./index_detail.php?action=add" class="btn btn-primary">
                        Create New Publication
                    </a>
                </div>
            </div>

            <form method="post">
                <div class="row">
                    <div class="mb-3 col-md-3">
                        <div class="form-group">
                            <label for="abbreviation">Abbreviation:</label>
                            <input type="text" class="form-control" id="abbreviation" name="abbreviation" value="<?= $abbreviation ?>" maxlength="7" required>
                        </div>
                    </div>
                    <div class="mb-3 col-md-9">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= $name ?>" maxlength="100" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
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
        <?= $nameErr; ?>
    </script>

</body>

</html>