<?php

$SECURITY->isLoggedIn();

use gnome\classes\model\Indices;
use gnome\classes\model\PublicationIndex;
use gnome\classes\model\Publication;

$lang = lang();

$Indices = new Indices();
$PublicationIndex = new PublicationIndex();
$indices_id = filter_input(INPUT_GET, 'index_id');
$pub_type = filter_input(INPUT_GET, 'pub_type') ? filter_input(INPUT_GET, 'pub_type') : null;
$publicationName = null;
$publicationAbbr = null;

// $indices_id = ''; passed in
$name = '';
$description_html = '';
$highlight_color = '';
$text_color = '';

if ($indices_id) {
    $SECURITY->indexPermission($indices_id)?->hasRightAccess('can_write', 'Author access needed');

    // gets indices information html css color
    $indexRs =  $Indices->getIndex($indices_id);
    extract($indexRs);

    if (!is_null($pub_type)) {
        $publicationIndexRs = $PublicationIndex->getIndexPublications($indices_id, $pub_type);
    }

    $Publication = new Publication();
    $publicationTypes = $Publication->getPublicationTypes();

    if (!is_null($pub_type)) {
        $publicationType = $Publication->getPublicationType($pub_type);
        $publicationName = $publicationType['name'];
        $publicationAbbr = $publicationType['abbreviation'];
    }

} else {
    $_SESSION['actionResponse'] = "No index was selected";
    header("Location: ./indices.php?lang=$lang");
    exit();
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
            <h1>Indices</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/gnome/index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/indices.php">Indices (<?= $name ?>)</a>
                    </li>
                    <?php if (is_null($pub_type))  :?>
                    <li class="breadcrumb-item active">
                        Select Publication Type
                    </li>
                    <?php else:?>
                     <li class="breadcrumb-item active">
                        <?= $publicationName; ?> (<?= $publicationAbbr; ?>)
                    </li>
                    <?php endif;?>
                    
                </ol>
            </nav>
        </div>
        <!-- End Page Title -->
        <section class="section" id="wrapper">

            <header class="main">
                <h4>You are viewing: <?= $name; ?></h4>
                <!-- <div>
                    <h2 class="p-3" style="width:fit-content; border-radius:6px; 
                                    color:<?= $text_color; ?>; 
                                    background-color:<?= $highlight_color; ?>;">
                        
                    </h2>
                </div> -->
            </header>
            <div>

                <div class="row">
                    <div class="col-12 mt-2 mb-3">
                        <?php
                        $selectedPublicationType = 'Select publication type';
                        $htmlPublicationType = '';

                        foreach ($publicationTypes as $key => $value) {
                            if ($value['publication_type_id'] == $pub_type) {
                                $selectedPublicationType = $value['name'];
                            }
                            $htmlPublicationType .= "<li class='form-check'>
                                <a href=\"/gnome/indexer/indexlinks.php?index_id={$indices_id}&lang={$lang}&pub_type={$value['publication_type_id']}\" data-toggle=\"tooltip\" data-original-title=\"{$value['abbreviation']}\">{$value['name']}</a>
                            </li>";
                        }
                        ?>

                        <div class="dropdown">
                            <a class="btn btn-outline-dark btn-lg dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $selectedPublicationType; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <?php echo $htmlPublicationType; ?>
                            </ul>
                        </div>

                        <div class="mt-3"><strong>To start:</strong>
                            <ol>
                                <li>Select a publication type.</li>
                                <li>See status | Start editing | Continue editing
                                    <button class="btn btn-sm" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-sanitize="false" data-bs-html="true" data-bs-content='<div class="popover fs-6" role="tooltip">
                                                <div class="popover-body" >
                                                    <img src="/gnome/assets/img/how-to-publications-info.jpg" /><br>
                                                    <a href="/gnome/indexer/publications_index.php?lang=en">To Publications Index</a>
                                                </div></div>'>
                                        <i class="bi bi-info-circle-fill"></i>
                                    </button>
                                </li>
                            </ol>
                        </div>





                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-2 mb-3">
                        <div class="legend">Legend:
                            <div class="not-started"></div> Not Started,
                            <div class="inprogress"></div> In progress,
                            <div class="needs-review"></div> Review needed,
                            <div class="being-reviewed"></div> Review in progress,
                            <div class="no-ref-found"></div> Finished no ref found,
                            <div class="finished"></div> Finished
                        </div>
                    </div>
                </div>
                <div class="row gx-2 gy-2">
                    <?php if (is_null($pub_type)) : ?>
                        <div class="col-12">

                            <div class="mb-5 mt-5" style="text-align:center">
                                <strong>Select a publication type to begin</strong><br>
                                <div class="dropdown">
                                    <a class="btn btn-outline-dark btn-lg dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo $selectedPublicationType; ?>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <?php echo $htmlPublicationType; ?>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    <?php endif; ?>
                    <?php foreach ($publicationIndexRs as $key => $value) : ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                            <div class="card mb-0 p-2 <?php echo strToCss($value['indexing_status']); ?>">
                                <div class="d-flex justify-content-between align-items-center">

                                    <strong>
                                        <a href="./pub_view_index.php?publication_id=<?= $value['publication_id'] ?>&index_id=<?= $indices_id ?>&pub_type=<?=$pub_type ?>" class="">
                                            <?php echo $value['publication_id']; ?>
                                        </a>
                                    </strong>


                                    <?php
                                    if ($value['indexing_status'] != "Not Started") :
                                    ?>
                                        <a href="./pub_index_editor.php?publication_id=<?= $value['publication_id'] ?>&index_id=<?= $indices_id ?>&pub_type=<?= $pub_type ?>" class="update-btn <?php echo strToCss($value['indexing_status']); ?>">edit</a>
                                    <?php elseif ($value['is_ready'] != 1) : ?>
                                        <span class="nt-ready">Not ready!</span>
                                    <?php else : ?>
                                        <a onClick="initEditing('<?= $value['publication_id'] ?>','<?= $indices_id ?>','<?= $pub_type ?>')" href="javascript:return false;" class="update-btn <?php echo strToCss($value['indexing_status']); ?>">Start</a>
                                    <?php endif; ?>

                                </div>
                                <div class="pt-2">
                                    <div class="dynamic-sm"><span>Status:</span> <br>
                                        <?php echo $value['indexing_status']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/../includes/footer.inc.php'; ?>

    <script>
        const initEditing = (_publication_id, _index_id, _pub_type) => {
            const index_id = _index_id;
            const publication_id = _publication_id;
            const pub_type = _pub_type;
            const tracks = '';
            const summary = '';
            const keyWords = '';
            const notes = '';
            const publicationStatus = 'inprogress';
            const optionalFieldsAns = '';
            const optionalFieldsArr = '';

            // console.log(optionalFieldsArr)
            jsonData = {
                index_id,
                publication_id,
                tracks,
                keyWords,
                publicationStatus,
                optionalFieldsArr,
                notes,
                summary
            };
            // console.log(jsonData)

            $.ajax({
                type: "POST",
                url: "publication_ajax.php", // "/gnome/upload_html.php",
                data: jsonData,
                dataType: 'json',
                cache: false,
                success: function(html) {
                    console.log('save complete');
                    $(location).attr("href",
                        `/gnome/indexer/pub_index_editor.php?publication_id=${publication_id}&index_id=${index_id}&pub_type=${pub_type}`
                    );
                }
            });

        }
    </script>

</body>

</html>