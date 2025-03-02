<?php

$SECURITY->isLoggedIn();


use gnome\classes\model\PublicationIndex as PublicationIndex;
use gnome\classes\model\Publication as Publication;
use gnome\classes\model\Indices as Indices;

// require_once(__DIR__ . '/../includes/crystal/functions.php');

$lang = lang();

$PublicationIndex = new PublicationIndex();
$Publication = new Publication();
$Indices = new Indices();


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
    $publication_index_id =
    $notes = '';



$indices_id = filter_input(INPUT_GET, 'index_id') ? filter_input(INPUT_GET, 'index_id') : "";
$publication_id = filter_input(INPUT_GET, 'publication_id') ? filter_input(INPUT_GET, 'publication_id') : "";

// $id = filter_input(INPUT_GET, 'id') ? filter_input(INPUT_GET, 'id') : "";
$pub_type = filter_input(INPUT_GET, 'pub_type') ? filter_input(INPUT_GET, 'pub_type') : "";


if ($indices_id === "" || $publication_id === "") {
    $_SESSION['actionResponse'] = "Either Publication or Index was not selected, Try again";
    header("Location: indices.php");
    exit();
}

$name = '';
$description_html = '';
$highlight_color = '';
$text_color = '';

$indexRs =  $Indices->getIndex($indices_id);
extract($indexRs);

// Select Statements:
$publication = $Publication->getPublication($publication_id);
$optionalFieldsAns = $Indices->getOptionalFieldsWithAnswer($indices_id, $publication_id);
$publicationIndex = (object) $PublicationIndex->getIndexPublication($indices_id, $publication_id);
$keywordsMeta = $PublicationIndex->getIndexKeywordMeta($indices_id);


extract($publication);

$statusLookup = $PublicationIndex->getPublicationStatusLookup();

?>
<!DOCTYPE html>
<html>


<head>
    <?php include __DIR__ . '/../includes/head.inc.php'; ?>

    <style id="eNum">
        /** test enumeration change */
        .eNum.on::before {
            content: attr(id);
            color: var(--rgba-primary-0);
            font-weight: bold;
        }

        .eNum::before {
            content: '';
        }


        .highlighter {
            color: <?= $text_color; ?>;
            background-color: <?= $highlight_color; ?>;
        }
    </style>
</head>

<body class="">
    <?php include __DIR__ . '../../includes/topnav.inc.php'; ?>
    <?php include '../includes/sidebar.inc.php'; ?>
    <main id="main" class="main">
        <?php include '../includes/title.inc.php'; ?>
        <div class="pagetitle">
            <h1>Publication : <strong><?= $publication_id ?></strong></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/gnome/index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/indices.php">Indices</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/indexlinks.php?index_id=<?= $indices_id ?>&pub_type=<?= $pub_type ?>&lang=<?= $lang ?>">Indices</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Index(<?= $name ?>)
                    </li>
                </ol>
            </nav>
        </div>
        <section class="section" id="wrapper">
            <header class="main">
                <h4>You are viewing: </h4>
                <div>
                    <h2 class="p-3" style="width:fit-content; border-radius:6px; 
                                    color:<?= $text_color; ?>; 
                                    background-color:<?= $highlight_color; ?>;">
                        <?= $name; ?>
                    </h2>
                </div>
            </header>
            <div class="mt-3 mb-3" id="publication_keywords_display">
                <h3>Keywords : </h3>
                <div class="chip-container">
                <?php
                $keyWords = is_null($publicationIndex->keywords) ? [] : $publicationIndex->keywords;

                foreach ($keyWords as $word) :
                    $dataSelected = [];
                    $metahtml = '';
                    foreach ($word->metas as $meta) {

                        $dataSelected[] = $meta->id;
                        $metahtml .= "<li class='sub-meta-$meta->id'>
                                                            <strong>$meta->value</strong>
                                                        </li>";
                    }
                ?>
                    <div class="chip" id="chip-keyword-<?= $word->id ?>">
                        <?= $word->value ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
            <div class="mt-3 mb-3" id="publication_questions_display">
                <h3>Questions answered : </h3>
                <?php foreach ($optionalFieldsAns as $row) : ?>
                    <div class="chip <?= $row["optional_field_value"] == 1 ? 'yes' : 'no' ?>">
                        <?= $row["optional_field"] ?> - <strong><?= $row["optional_field_value"] == 1 ? 'YES' : 'NO' ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3 mb-3" id="publication_display">
                <div class="row">
                    <div class="col-12" id="publication_container">
                        <?= $raw_html ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>
        // const toggleEnumeration = document.querySelector("#toggle-visability");
        // const para = document.querySelectorAll(".eNum");

        // toggleEnumeration.addEventListener("click", function() {
        //     para.forEach((element) => {
        //         element.classList.toggle("on")
        //     })
        // });


        const initHighlighter = () => {
            const tracks = "<?= trim($publicationIndex->tracks) ?>";
            if (tracks == '') return;
            const tracksArr = tracks.split(',');
            tracksArr.forEach((item) => {
                let engTxt = document.getElementById(`${item}en`);
                let gerTxt = document.getElementById(`${item}de`);
                engTxt.classList.add('highlighter');
                gerTxt.classList.add('highlighter');
            })
        }


        initHighlighter();
    </script>

    <?php include __DIR__ . '/../includes/script.import.inc.php'; ?>
    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>

</body>

</html>