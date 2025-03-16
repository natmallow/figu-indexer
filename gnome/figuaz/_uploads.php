<?php

$SECURITY->isLoggedIn();

$files = require_once(__DIR__ . '/upload_mapping.php');

$lang = empty($_GET['lang']) ? 'en' : $_GET['lang'];

?>
<!doctype html>

<html lang="en">

<head>
    <?php include __DIR__ . '/includes/head.inc.php'; ?>
</head>

<body class="">
    <?php include __DIR__ . '/includes/topnav.inc.php'; ?>
    <?php include 'includes/sidebar.inc.php'; ?>
    <main id="main" class="main">


        <?php include 'includes/title.inc.php'; ?>

        <section class="section">
            <header class="py-3 mb-4 border-bottom">
                <div class="col-12">
                    <span class="fs-4">Upload Manager</span>
                </div>
            </header>

            <div class="row">
                <div class="col-md-12">
                    <?php include 'includes/head-resp.inc.php'; ?>
                </div>
            </div>


            <form class="row" action="uploader.php" method="post" enctype="multipart/form-data">

                <div class="col-md-6">
                    <div class="input-group mb-3"> 
                        <label class="input-group-text" for="fileToUpload">Select file to upload</label>
                        <input type="file" class="form-control" name="fileToUpload" multiple id="fileToUpload">
                       
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <select type="text" name="directory" class="form-select" id="directory" required>
                            <option value="">Choose Upload Directory</option>
                            <?php foreach ($files['dirs'] as $item) : ?>
                                <option value="<?= $item ?>"><?= $item ?></option>
                            <?php endforeach ?>
                        </select>
                        <button type="submit" class="btn btn-outline-secondary" value="Upload Image" name="submitted">Upload</button>
                    </div>
                </div>


                <div class="col-12">
                    <button id="openDirectoryView" type="button" class="btn btn-outline-secondary">View Files</button>
                </div>
            </form>
            <hr>


            <div>
                <div class="box alt" id='container-image' style="height: 45vh; overflow-y:auto">
                    <div class="row gtr-50 gtr-uniform" id='container-dir'>

                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include __DIR__ . '/../../includes/script.image.inc.php'; ?>
    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/../includes/footer.inc.php'; ?>
    <script>
        function factory() {
            initModal('container-image', 'openDirectoryView', 'closeModal', 'container-dir', '', ['imageDeleteHandler']);
        }

        window.onload = factory();
    </script>
</body>

</html>
<?php
$_SESSION['actionResponse'] = '';
?>